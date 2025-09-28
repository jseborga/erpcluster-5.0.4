<?php
$now = dol_now();
	//importitem
if ($action == 'importitem')
{
	$objectadd->fetch(0,$id);

	$aSel = GETPOST('sel');
	$element = GETPOST('element');
				// For compatibility
	$object_element = $element;
	if ($element == 'order')    {
		$element = $subelement = 'commande';
	}
	if ($element == 'propal')   {
		$element = 'comm/propal'; $subelement = 'propal';
	}
	if ($element == 'contract') {
		$element = $subelement = 'contrat';
	}
	if ($element == 'order_supplier') {
		$element = 'purchase'; $subelement = 'fournisseurcommandeext';
				//$element = 'purchase'; $subelement = 'fournisseurcommandeext';
	}
	if ($element == 'project')
	{
		$element = 'projet';
	}
	require_once DOL_DOCUMENT_ROOT.'/'.$element.'/class/'.$subelement.'.class.php';
	$classname = ucfirst($subelement);

	if ($classname == 'Fournisseur.commande') $classname='CommandeFournisseurLigne';
	if ($classname == 'fournisseur.commande') $classname='CommandeFournisseurLigne';
	if ($classname == 'Fournisseurcommandeext') $classname='CommandeFournisseurLigneext';

	$srcobject = new $classname($db);
	$aTotaltav[$id] = array();


	$db->begin();
	foreach ($aSel AS $idsel => $value)
	{
		$i = 0;
			//recuperamos el item del pedido
		$result=$srcobject->fetchline($idsel);

		$lines[$i] = $srcobject;

			//preparamos para relacionar la factura con el origen
			//1 buscamos la cabecera del item
		if ($object_element == 'order_supplier')
		{
			$object->origin_id = $lines[$i]->fk_commande;
			$object->origin = $object_element;
			$res = $object->addobject_linked();
			if ($res>0)
				setEventMessages($langs->trans('agregado elemento s vinculados'),null,'mesgs');
		}
		$productadd->fetch(0,$lines[$i]->fk_product);
		$desc=($lines[$i]->desc?$lines[$i]->desc:$lines[$i]->libelle);
		$product_type=($lines[$i]->product_type?$lines[$i]->product_type:0);

						// Dates
						// TODO mutualiser
		$date_start=$lines[$i]->date_debut_prevue;
		if ($lines[$i]->date_debut_reel) $date_start=$lines[$i]->date_debut_reel;
		if ($lines[$i]->date_start) $date_start=$lines[$i]->date_start;
		$date_end=$lines[$i]->date_fin_prevue;
		if ($lines[$i]->date_fin_reel) $date_end=$lines[$i]->date_fin_reel;
		if ($lines[$i]->date_end) $date_end=$lines[$i]->date_end;

		//procesamos el calculo de los impuestos
		$tvacalc = array();
		$tvaht = array();
		$tvattc = array();
		$tvatx = array();
		$k = 1;
		$qty = $lines[$i]->qty;
		$pu = $lines[$i]->subprice;
		$price_base_type = 'HT';
		if ($conf->global->PRICE_TAXES_INCLUDED)
		{
			$price_base_type = 'TTC';
			$pu = $lines[$i]->price;
			$lines[$i]->price = $pu;
		}
		$lines[$i]->fk_unit+=0;
		include DOL_DOCUMENT_ROOT.'/fiscal/include/calclinesfiscal.inc.php';

		$result = $object->addlineadd(
			$desc,
			$lines[$i]->subprice,
			$lines[$i]->price,
			$lines[$i]->tva_tx,
			$lines[$i]->localtax1_tx,
			$lines[$i]->localtax2_tx,
			$lines[$i]->qty,
			$lines[$i]->fk_product,
			$lines[$i]->remise_percent,
			$date_start,
			$date_end,
			0,
			$lines[$i]->info_bits,
			$type_price,
			$product_type,
			-1,
			false,
			$lines[$i]
		);

		if ($result < 0)
		{
			$error++;
			setEventMessages($object->error,$object->errors,'errors');
			break;
		}
		$idligne = $object->rowid;
		foreach ((array) $tvacalc AS $code => $value)
		{
			$objectdetfiscal->fk_facture_fourn_det = $idligne;
			$objectdetfiscal->code_tva = $code;
			$objectdetfiscal->tva_tx = $tvatx[$code];
			$objectdetfiscal->total_tva = $value;
			$objectdetfiscal->total_ht = $tvaht[$code];
			$objectdetfiscal->total_ttc = $tvattc[$code];
			$objectdetfiscal->amount_base = $pricebase;
			$objectdetfiscal->fk_user_create = $user->id;
			$objectdetfiscal->fk_user_mod = $user->id;
			$objectdetfiscal->date_create = dol_now();
			$objectdetfiscal->date_mod = dol_now();
			$objectdetfiscal->tms = dol_now();
			$objectdetfiscal->status = 1;
			$resf = $objectdetfiscal->create($user);
			if ($resf <=0)
			{
				$error++;
				setEventMessages($objectdetfiscal->error,$objectdetfiscal->errors,'errors');
				break;
			}
			if ($code == 'IVA')
			{
				$aTotaltav[$id]['tva_tx']	=$tvatx[$code];
				$aTotaltav[$id]['total_tva']	+=$value;
			}
			$aTotaltav[$id]['total_ht']	+=$tvaht[$code];
			$aTotaltav[$id]['total_ttc']	+=$tvattc[$code];
		}
						//agregamos a la tabla adicional objectdetadd
		$objectdetadd->fk_facture_fourn_det = $idligne;
		$objectdetadd->fk_object = $lines[$i]->rowid;
		$objectdetadd->object = $object_element;
		$objectdetadd->amount_ice = 0;
		$objectdetadd->discount = 0;
		$objectdetadd->fk_user_create = $user->id;
		$objectdetadd->fk_user_mod = $user->id;
		$objectdetadd->date_create = dol_now();
		$objectdetadd->date_mod = dol_now();
		$objectdetadd->tms = dol_now();
		$objectdetadd->status = 1;
		$resdadd = $objectdetadd->create($user);
		if ($resdadd<=0)
		{
			setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
			$error++;
		}
	}

		//actualizamos en facture_fourn
	include DOL_DOCUMENT_ROOT.'/purchase/include/update_facture_fourn.inc.php';

	if (!$error)
	{
		$db->commit();
		setEventMessages($langs->trans('Importsucessfull'),null,'mesgs');
		header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
		exit;
	}
	else
	{
		$db->rollback();
		$action = '';
	}
}

	// Link invoice to order
if (GETPOST('linkedOrder') && empty($cancel) && $id > 0)
{

	$object->fetch($id);
	$object->fetch_thirdparty();
	$result = $object->add_object_linked('order_supplier', GETPOST('linkedOrder'));
}

	// Action clone object
if ($action == 'confirm_clone' && $confirm == 'yes')
{
	$result=$object->createFromClone($id);
	if ($result > 0)
	{
		header("Location: ".$_SERVER['PHP_SELF'].'?action=editref_supplier&id='.$result);
		exit;
	}
	else
	{
		$langs->load("errors");
		setEventMessages($langs->trans($object->error), null, 'errors');
		$action='';
	}
}

elseif ($action == 'confirm_valid' && $confirm == 'yes' && ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->fournisseur->facture->creer)) || (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->fournisseur->supplier_invoice_advance->validate))))
{
	$idwarehouse=GETPOST('idwarehouse');

	$filter = " AND t.rowid = ".$id;
	$object 		= new FactureFournisseurext($db);
	$object->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
	$object->fetch_thirdparty();
	//vamos a agregar segun el detalle de la factura
	$lines = $object->lines[$id]->lines;

	$qualified_for_stock_change=0;
	if (empty($conf->global->STOCK_SUPPORTS_SERVICES))
	{
		$qualified_for_stock_change=$object->hasProductsOrServices(2);
	}
	else
	{
		$qualified_for_stock_change=$object->hasProductsOrServices(1);
	}

		// Check parameters
	if (! empty($conf->stock->enabled) && ! empty($conf->global->STOCK_CALCULATE_ON_SUPPLIER_BILL) && $qualified_for_stock_change)
	{
		$langs->load("stocks");
		if (! $idwarehouse || $idwarehouse == -1)
		{
			$error++;
			setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentitiesnoconv("Warehouse")), null, 'errors');
			$action='';
		}
	}

	if (! $error)
	{
		$db->begin();
		$result = $object->validate($user,'',$idwarehouse);
		if ($result < 0)
		{
			setEventMessages($object->error,$object->errors,'errors');
		}
		if ($conf->global->PURCHASE_INTEGRATED_POA)
		{
			//se creara registro de devengado
			if ($conf->poa->enabled)
			{
				//debemos obtener el preventivo de la compra
				$targettype = 'invoice_supplier';
				$idtype = $id;
				$lLoop = true;
				$fk_commande_fourn = 0;
				$fk_area = 0;
				while ($lLoop == true)
				{
					$aElement = getElementElement($targettype,$idtype,'target');
					if (count($aElement)>0)
					{
						$targettype = $aElement[0]['sourcetype'];
						$idtype = $aElement[0]['fk_source'];
						if ($targettype == 'order_supplier')
						{
							require_once DOL_DOCUMENT_ROOT.'/purchase/class/fournisseurcommandeext.class.php';
							require_once DOL_DOCUMENT_ROOT.'/purchase/class/commandefournisseuradd.class.php';
							$objCommande = new FournisseurCommandeext($db);
							$objCommande->fetch($idtype);
							$fk_commande_fourn = $idtype;

							$objCommandeadd = new FournisseurCommandeext($db);
							$objCommandeadd->fetch(0,$idtype);
							$fk_poa_prev = $objCommande->fk_poa_prev;
							$fk_area = $objCommandeadd->fk_departament;
							if (empty($fk_poa_prev)) $fk_poa_prev = $objCommandeadd->fk_poa_prev;
						}
						elseif($targettype=='purchaserequest')
						{
							require_once DOL_DOCUMENT_ROOT.'/purchase/class/purchaserequestext.class.php';
							$objRequest = new Purchaserequestext($db);
							$objRequest->fetch($idtype);
							$fk_poa_prev = $objRequest->fk_poa_prev;
							$fk_area = $objRequest->fk_departament;
							if (empty($fk_poa_prev)) $fk_poa_prev = $objRequest->fk_poa_prev;
						}
					}
					else
						$lLoop = false;
				}
			}
		}
		if ($fk_poa_prev>0)
		{

			// Possibility to add external linked objects with hooks
			$resl = $object->add_object_linked('poa_prev',$fk_poa_prev);
			if ($resl<=0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
			require_once DOL_DOCUMENT_ROOT.'/poa/class/poapartidacomext.class.php';
			require_once DOL_DOCUMENT_ROOT.'/poa/class/poapartidadevext.class.php';
			require_once DOL_DOCUMENT_ROOT.'/poa/class/poaprevext.class.php';
			$objPoaprev = new Poaprevext($db);
			$objPartidacom = new Poapartidacomext($db);
			$objPartidadev = new Poapartidadevext($db);

			$objPoaprev->fetch($fk_poa_prev);
			//cambiamos de estado
			$objPoaprev->statut = 3;
			$resprev = $objPoaprev->update_status($user);
			if ($resprev<=0)
			{
				$error++;
				setEventMessages($objPoaprev->error,$objPoaprev->errors,'errors');
			}
			$aDev = array();
			foreach ($lines AS $j => $line)
			{
				//echo '<hr>lineeeeee '.$line->fk_structure.' '.$line->fk_poa.' '.$line->total_ttc;
				$aDev[$line->fk_structure][$line->fk_poa][$line->partida]+=$line->total_ttc;
			}
			//creamos el devengado en base a aDev
			foreach ((array) $aDev AS $fk_structure => $aDevdet)
			{
				foreach ((array) $aDevdet AS $fk_poa => $aPartida)
				{
					foreach ((array) $aPartida AS $partida => $value)
					{
						//buscamos el comprometido, segun el pedido
						$filtercom = " AND t.fk_poa_prev = ".$fk_poa_prev;
						$filtercom.= " AND t.fk_structure = ".$fk_structure;
						$filtercom.= " AND t.fk_poa = ".$fk_poa;
						$filtercom.= " AND t.fk_contrato = ".$fk_commande_fourn;
						$filtercom.= " AND t.partida = '".$partida."'";
						$rescom = $objPartidacom->fetchAll('','',0,0,array(1=>1),'AND',$filtercom,true);
						if ($rescom==1)
							$fk_poa_partida_com = $objPartidacom->id;
						//obtenemos el numero dev maximo
						$res = $objPartidadev->get_maxref($objPoaprev->gestion,$fk_area);
						if ($res >0) $nro_dev = $objPartidadev->maximo;
						$objPartidadev->fk_poa_partida_com = $fk_poa_partida_com;
						$objPartidadev->gestion = $objPoaprev->gestion;
						$objPartidadev->fk_poa_prev = $fk_poa_prev;
						$objPartidadev->fk_structure = $fk_structure;
						$objPartidadev->fk_poa = $fk_poa;
						$objPartidadev->fk_contrat = $objPartidacom->fk_contrat;
						$objPartidadev->fk_contrato = $fk_commande_fourn;
						$objPartidadev->type_pay = 0;
						$objPartidadev->nro_dev = $nro_dev;
						$objPartidadev->partida = $partida;
						$objPartidadev->invoice = $object->ref;
						$objPartidadev->amount = $value;
						$objPartidadev->date_dev = $object->lines[$id]->date;
						$objPartidadev->date_create = $object->lines[$id]->date;
						$objPartidadev->fk_user_create = $user->id;
						$objPartidadev->fk_user_mod = $user->id;
						$objPartidadev->datec = $now;
						$objPartidadev->datem = $now;
						$objPartidadev->tms = $now;
						$objPartidadev->statut = 1;
						$objPartidadev->active = 1;
						$resc = $objPartidadev->create($user);
						if ($resc<=0)
						{
							$error++;
							setEventMessages($objPartidadev->error,$objPartidadev->errors,'errors');
						}
					}
				}
			}
		}

		if (!$error)
		{
			$db->commit();
			header('Location: '.$_SERVER['PHP_SELF'].'?facid='.$id);
			exit;
		}
		else
			$db->rollback();
		$action = '';
	}
}

elseif ($action == 'confirm_delete' && $confirm == 'yes' && $user->rights->fournisseur->facture->supprimer)
{
	$object->fetch($id);
	$object->fetch_thirdparty();
	$result=$object->delete($id);
	if ($result > 0)
	{
		header('Location: list.php');
		exit;
	}
	else
	{
		setEventMessages($object->error, $object->errors, 'errors');
	}
}
	// Remove a product line
else if ($action == 'confirm_deleteline' && $confirm == 'yes' && $user->rights->fournisseur->facture->creer)
{
	$db->begin();
	$error=0;
	//buscamos en fiscal
	$filter = " AND t.fk_facture_fourn_det = ".$lineid;
	$resf = $objectdetfiscal->fetchAll('','',0,0,array(1=>1),'AND',$filter);
	if($resf>0)
	{
		$lines = $objectdetfiscal->lines;
		foreach ($lines AS $j => $line)
		{
			$objectdetfiscal->fetch($line->id);
			$resf = $objectdetfiscal->delete($user);
			if ($resf <=0)
			{
				$error++;
				setEventMessages($objectdetfiscal->error,$objectdetfiscal->errors,'errors');
			}
		}
	}
	if (!$error)
	{
		$result = $object->deleteline($lineid);

		if ($result > 0)
		{
			$res = $objectdetadd->fetch(0,$lineid);
			$res = $objectdetadd->delete($user);
			if ($res<=0)
			{
				$error++;
				setbankaccount($objectdetadd->error,$objectdetadd->errors,'errors');
			}
			if (!$error)
			{
			//eliminamos de facturefourndetfiscal
				$filter = " AND t.fk_facture_fourn_det = ".$lineid;
				$res = $objectdetfiscal->fetchAll('','',0,0,array(1=>1),'AND',$filter);
				if ($res>0)
				{
					$lines = $objectdetfiscal->lines;
					foreach ($lines AS $j => $line)
					{
						if ($objectdetfiscal->fetch($line->id))
						{
							$resdf = $objectdetfiscal->delete($user);
							if ($resdf<=0)
							{
								$error++;
								setbankaccount($objectdetfiscal->error,$objectdetfiscal->errors,'errors');
							}
						}
					}
			//actualizamos en facture_fourn
				}
			}
			include DOL_DOCUMENT_ROOT.'/purchase/include/update_facture_fourn.inc.php';
			// Define output language
			$outputlangs = $langs;
			$newlang = '';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id'))
				$newlang = GETPOST('lang_id');
			if ($conf->global->MAIN_MULTILANGS && empty($newlang))
				$newlang = $object->thirdparty->default_lang;
			if (! empty($newlang)) {
				$outputlangs = new Translate("", $conf);
				$outputlangs->setDefaultLang($newlang);
			}
			if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE)) {
				$ret = $object->fetch($object->id);
				// Reload to get new records
				$object->generateDocument($object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
			}

		}
		else
		{
			setEventMessages($object->error, $object->errors, 'errors');
			/* Fix bug 1485 : Reset action to avoid asking again confirmation on failure */
			$action='';
		}
	}
	if (!$error)
	{
		$db->commit();
		header('Location: '.$_SERVER["PHP_SELF"].'?id='.$object->id);
		exit;
	}
	else
		$db->rollback();

}

elseif ($action == 'confirm_paid' && $confirm == 'yes' && $user->rights->fournisseur->facture->creer)
{
	$object->fetch($id);
	$result=$object->set_paid($user);
	if ($result<0)
	{
		setEventMessages($object->error, $object->errors, 'errors');
	}
}

	// Set supplier ref
if ($action == 'setref_supplier' && $user->rights->fournisseur->commande->creer)
{
	$object->ref_supplier = GETPOST('ref_supplier', 'alpha');

	if ($object->update($user) < 0) {
		setEventMessages($object->error, $object->errors, 'errors');
	}
}

	// payments conditions
if ($action == 'setconditions' && $user->rights->fournisseur->commande->creer)
{
	$result=$object->setPaymentTerms(GETPOST('cond_reglement_id','int'));
}

	// payment mode
elseif ($action == 'setmode' && $user->rights->fournisseur->commande->creer)
{
	$result = $object->setPaymentMethods(GETPOST('mode_reglement_id','int'));
}

	// Multicurrency Code
elseif ($action == 'setmulticurrencycode' && $user->rights->facture->creer)
{
	$result = $object->setMulticurrencyCode(GETPOST('multicurrency_code', 'alpha'));
}

	// Multicurrency rate
elseif ($action == 'setmulticurrencyrate' && $user->rights->facture->creer)
{
	$result = $object->setMulticurrencyRate(price2num(GETPOST('multicurrency_tx')));
}

	// bank account
elseif ($action == 'setbankaccount' && $user->rights->fournisseur->facture->creer)
{
	$result=$object->setBankAccount(GETPOST('fk_account', 'int'));
}

	// Set label
elseif ($action == 'setlabel' && $user->rights->fournisseur->facture->creer)
{
	$object->fetch($id);
	$object->label=$_POST['label'];
	$result=$object->update($user);
	if ($result < 0) dol_print_error($db);
}
elseif ($action == 'setdatef' && $user->rights->fournisseur->facture->creer)
{
	$object->fetch($id);
	$object->date=dol_mktime(12,0,0,$_POST['datefmonth'],$_POST['datefday'],$_POST['datefyear']);
	if ($object->date_echeance && $object->date_echeance < $object->date) $object->date_echeance=$object->date;
	$result=$object->update($user);
	if ($result < 0) dol_print_error($db,$object->error);
}
elseif ($action == 'setdate_lim_reglement' && $user->rights->fournisseur->facture->creer)
{
	$object->fetch($id);
	$object->date_echeance=dol_mktime(12,0,0,$_POST['date_lim_reglementmonth'],$_POST['date_lim_reglementday'],$_POST['date_lim_reglementyear']);
	if (! empty($object->date_echeance) && $object->date_echeance < $object->date)
	{
		$object->date_echeance=$object->date;
		setEventMessages($langs->trans("DatePaymentTermCantBeLowerThanObjectDate"), null, 'warnings');
	}
	$result=$object->update($user);
	if ($result < 0) dol_print_error($db,$object->error);
}

	// Delete payment
elseif ($action == 'confirm_delete_paiement' && $confirm == 'yes' && $user->rights->fournisseur->facture->creer)
{
	$object->fetch($id);
	if (
		$object->statut == FactureFournisseur::STATUS_VALIDATED && $object->paye == 0)
	{
		$paiementfourn = new PaiementFourn($db);
		$result=$paiementfourn->fetch(GETPOST('paiement_id'));
		if ($result > 0) {
			$result=$paiementfourn->delete();
				// If fetch ok and found
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
		}
		if ($result < 0) {
			setEventMessages($paiementfourn->error, $paiementfourn->errors, 'errors');
		}
	}
}

// Create
elseif ($action == 'confirm_add' && $user->rights->fournisseur->facture->creer)
{
	$error=0;
	$aPost = unserialize($_SESSION['aPost']);
	$_POST = $aPost[$id];
print_r($_POST);
	$datefacture=dol_mktime(12,0,0,$_POST['remonth'],$_POST['reday'],$_POST['reyear']);
	$datedue=dol_mktime(12,0,0,$_POST['echmonth'],$_POST['echday'],$_POST['echyear']);
		//recuperamos datos
	$lNit = false;
	if (GETPOST('code_facture'))
	{
		$res = $objTypefacture->fetch(0,GETPOST('code_facture'));
		if ($res == 1)
		{
			if ($objTypefacture->nit_required) $lNit = true;
		}
	}
	if ($lNit)
	{
		$ref_supplier = TRIM(GETPOST('num_autoriz')).'|';
		$ref_supplier.= TRIM(GETPOST('nit')).'|';
		$ref_supplier.= TRIM(GETPOST('nfiscal')).'|';
		$ref_supplier.= TRIM($datefacture).'|';
		$ref_supplier.= TRIM(GETPOST('cod_control'));
	}
	else
	{
		$ref_supplier = TRIM(GETPOST('num_autoriz_')).'|';
		$ref_supplier.= TRIM(GETPOST('nit_')).'|';
		$ref_supplier.= TRIM(GETPOST('nfiscal_')).'|';
		$ref_supplier.= TRIM($datefacture).'|';
		$ref_supplier.= TRIM(GETPOST('cod_control_'));
	}

	if (empty(GETPOST('code_facture'))|| GETPOST('code_facture')=='-1')
	{
		setEventMessage($langs->trans('ErrorFieldRequired',$langs->transnoentities('Typefiscal')), 'errors');
		$action='create';
		$_GET['socid']=$_POST['socid'];
		$error++;
	}
	if (empty(GETPOST('code_type_purchase'))|| GETPOST('code_type_purchase')=='-1')
	{
		setEventMessage($langs->trans('ErrorFieldRequired',$langs->transnoentities('Purchasedestination')), 'errors');
		$action='create';
		$_GET['socid']=$_POST['socid'];
		$error++;
	}
	if (GETPOST('socid','int')<1)
	{
		setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentities('Supplier')), null, 'errors');
		$action='create';
		$error++;
	}
	if ($lNit)
	{
		//vamos a validar el codigo de control
		$code_control = GETPOST('cod_control');
		if ($code_control != '0')
		{
			$aCodecontrol = explode('-',$code_control);
			if (dol_strlen($code_control)<11 || dol_strlen($code_control)>14)
			{
				$error++;
				setEventMessages($langs->trans('Complete el codigo de control'),null,'errors');
			}
			if (count($aCodecontrol)>0)
			{
				foreach ($aCodecontrol AS $j => $data)
				{
					if (dol_strlen($data)<2 || dol_strlen($data)>2)
					{
						$error++;
						setEventMessages($langs->trans('Falta o sobra caracteres en código de control'),null,'errors');
					}
				}
			}
		}
		if (empty(GETPOST('nit_company','alpha')))
		{
			setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentities('Nitcompany')), null, 'errors');
			$action='create';
			$error++;
		}
		if (empty(GETPOST('nfiscal','alpha')))
		{
			setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentities('Nfiscal')), null, 'errors');
			$action='create';
			$error++;
		}
		if (empty(GETPOST('num_autoriz','alpha')))
		{
			setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentities('Numautoriz')), null, 'errors');
			$action='create';
			$error++;
		}
	}
	if ($datefacture == '')
	{
		setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentities('DateFacture')), null, 'errors');
		$action='create';
		$_GET['socid']=$_POST['socid'];
		$error++;
	}
	if (! GETPOST('ref_supplier'))
	{
			//setEventMessages($langs->trans('ErrorFieldRequired',$langs->transnoentities('RefSupplier')), null, 'errors');
			//$action='create';
			//$_GET['socid']=$_POST['socid'];
			//$error++;
	}
	// Fill array 'array_options' with data from add form
	if (! $error)
	{
		$db->begin();

		$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);
		$ret = $extrafields->setOptionalsFromPost($extralabels, $object);
		if ($ret < 0) $error++;

		$tmpproject = GETPOST('projectid', 'int');

			// Creation facture
		$object->ref           = $_POST['ref'];
		$object->ref_supplier  = $ref_supplier;
		$object->socid         = $_POST['socid'];
		$object->libelle       = $_POST['label'];
		$object->date          = $datefacture;
		$object->date_echeance = $datedue;
		$object->note_public   = GETPOST('note_public');
		$object->note_private  = GETPOST('note_private');
		$object->cond_reglement_id = GETPOST('cond_reglement_id');
		$object->mode_reglement_id = GETPOST('mode_reglement_id');
		$object->fk_account        = GETPOST('fk_account', 'int');
		$object->fk_project    = ($tmpproject > 0) ? $tmpproject : null;
		$object->fk_incoterms = GETPOST('incoterm_id', 'int');
		$object->location_incoterms = GETPOST('location_incoterms', 'alpha');
		$object->multicurrency_code = GETPOST('multicurrency_code', 'alpha');
		$object->multicurrency_tx = GETPOST('originmulticurrency_tx', 'int');

			// Auto calculation of date due if not filled by user
		if(empty($object->date_echeance)) $object->date_echeance = $object->calculate_date_lim_reglement();

			// If creation from another object of another module
		if (! $error && $_POST['origin'] && $_POST['originid'])
		{
				// Parse element/subelement (ex: project_task)
			$element = $subelement = $_POST['origin'];
				//if (preg_match('/^([^_]+)_([^_]+)/i',$_POST['origin'],$regs))
				// {
				//$element = $regs[1];
				//$subelement = $regs[2];
				//}

				// For compatibility
			$object_element = $element;
			if ($element == 'order')    {
				$element = $subelement = 'commande';
			}
			if ($element == 'propal')   {
				$element = 'comm/propal'; $subelement = 'propal';
			}
			if ($element == 'contract') {
				$element = $subelement = 'contrat';
			}
			if ($element == 'order_supplier') {
				$element = 'fourn'; $subelement = 'fournisseur.commande';
				$element = 'purchase'; $subelement = 'fournisseurcommandeext';
			}
			if ($element == 'requestcashdeplacement') {
				$element = 'finint'; $subelement = 'requestcashdeplacementext';
			}
			if ($element == 'project')
			{
				$element = 'projet';
			}
			if ($element == 'projetpaiement')
			{
				$element = 'monprojet';
				$subelement = 'projetpaiementext';
			}

			$object->origin    = $_POST['origin'];
			$object->origin_id = $_POST['originid'];

			$id = $object->create($user);

				// Add lines
			if ($id > 0)
			{
					//agregamos a la tabla adicional
				$societe->fetch($object->socid);
				if ($societe->id == $object->socid) $objectadd->razsoc = $societe->name;
				$objectadd->fk_facture_fourn = $id;
				$objectadd->object = $_POST['origin'];
				$objectadd->fk_object = $_POST['originid'];
				$objectadd->nit_company = GETPOST('nit_company');
				$objectadd->code_facture = GETPOST('code_facture');
				$objectadd->fk_projet_task = GETPOST('fk_projet_task')+0;
				$objectadd->datec = dol_now();
				$objectadd->tms = dol_now();
				$objectadd->amount = 0;
				$objectadd->ndui = GETPOST('ndui','alpha');
				if ($lNit)
				{
					$objectadd->nit = GETPOST('nit');
					$objectadd->num_autoriz = GETPOST('num_autoriz');
					$objectadd->nfiscal = GETPOST('nfiscal');
					$objectadd->cod_control = GETPOST('cod_control');
				}
				else
				{
					$objectadd->nit = GETPOST('nit_');
					$objectadd->num_autoriz = GETPOST('num_autoriz_');
					$objectadd->nfiscal = GETPOST('nfiscal_');
					$objectadd->cod_control = GETPOST('cod_control_');
				}
					//echo '<hr>|'.$objectadd->nfiscal.'|';
				$objectadd->code_type_purchase = GETPOST('code_type_purchase');
				$objectadd->localtax3 = 0;
				$objectadd->localtax4 = 0;
				$objectadd->localtax5 = 0;
				$objectadd->localtax6 = 0;
				$objectadd->localtax7 = 0;
				$objectadd->localtax8 = 0;
				$objectadd->localtax9 = 0;

				require_once DOL_DOCUMENT_ROOT.'/'.$element.'/class/'.$subelement.'.class.php';
				$classname = ucfirst($subelement);
				if ($classname == 'Fournisseur.commande') $classname='CommandeFournisseur';
				if ($classname == 'fournisseurcommandeext') $classname='FournisseurCommandeext';
				$srcobject = new $classname($db);
				if ($element == 'finint' || $element == 'monprojet')
					$result=$srcobject->fetch($_POST['originid']);
				else
					$result=$srcobject->fetch_($_POST['originid']);

					//creamos los datos adicionales a facture_fourn
				$objectadd->fk_departament = $srcobject->fk_departament;

				if (empty($objectadd->fk_departament)) $objectadd->fk_departament = 0;

				$resadd = $objectadd->create($user);
				if ($resadd <= 0)
				{
					$error++;
					setEventMessages($objectadd->error,$objectadd->errors,'errors');
				}
					//si se recupero correctamente el $srcobject
				if ($result > 0)
				{
					$lines = $srcobject->lines;
					if (empty($lines) && method_exists($srcobject,'fetch_lines'))
					{
						$srcobject->fetch_lines();
						$lines = $srcobject->lines;
					}
					if (method_exists($srcobject,'fetch_linesadd'))
					{
						$srcobject->fetch_linesadd();
						$lines = $srcobject->lines;
					}
					$aTotaltav[$id] = array();
					$num=count($lines);
					for ($i = 0; $i < $num; $i++)
					{
						$productadd->fetch(0,$lines[$i]->fk_product);
						$desc=($lines[$i]->desc?$lines[$i]->desc:$lines[$i]->libelle);
						$product_type=($lines[$i]->product_type?$lines[$i]->product_type:0);

							// Dates
							// TODO mutualiser
						$date_start=$lines[$i]->date_debut_prevue;
						if ($lines[$i]->date_debut_reel) $date_start=$lines[$i]->date_debut_reel;
						if ($lines[$i]->date_start) $date_start=$lines[$i]->date_start;
						$date_end=$lines[$i]->date_fin_prevue;
						if ($lines[$i]->date_fin_reel) $date_end=$lines[$i]->date_fin_reel;
						if ($lines[$i]->date_end) $date_end=$lines[$i]->date_end;

							//procesamos el calculo de los impuestos
						$tvacalc = array();
						$tvaht = array();
						$tvattc = array();
						$tvatx = array();
						$k = 1;
						$qty = $lines[$i]->qty;
						$pu = $lines[$i]->subprice;
						$subprice = $lines[$i]->subprice;
						$price_base_type = 'HT';
						if ($conf->global->PRICE_TAXES_INCLUDED)
						{
							$price_base_type = 'TTC';
							$pu = $lines[$i]->price;
							//$lines[$i]->price = $pu;
						}
							//cuando se realiza la importacion y verificamos si el tipo de factura es 1
						require_once DOL_DOCUMENT_ROOT.'/fiscal/class/ctypefacture.class.php';
						$objtype = new Ctypefacture($db);
						$objtype->fetch(0,$objectadd->code_facture);
						if ($objtype->type_value == 1)
						{
							//el precio unitario debe enviarse como neto
							$pu = $subprice;
						}
						$lines[$i]->fk_unit+=0;
						include DOL_DOCUMENT_ROOT.'/fiscal/include/calclinesfiscal.inc.php';
						$result = $object->addlineadd(
							$desc,
							$lines[$i]->subprice,
							$lines[$i]->price,
							$lines[$i]->tva_tx,
							$lines[$i]->localtax1_tx,
							$lines[$i]->localtax2_tx,
							$lines[$i]->qty,
							$lines[$i]->fk_product,
							$lines[$i]->remise_percent,
							$date_start,
							$date_end,
							0,
							$lines[$i]->info_bits,
							$type_price,
							$product_type,
							-1,
							false,
							$lines[$i]
						);

						if ($result < 0)
						{
							$error++;
							setEventMessages($object->error,$object->errors,'errors');
							break;
						}
						$idligne = $object->rowid;

							//agregamos en detfiscal
						foreach ((array) $tvacalc AS $code => $value)
						{
							$objectdetfiscal->fk_facture_fourn_det = $idligne;
							$objectdetfiscal->code_tva = $code;
							$objectdetfiscal->tva_tx = $tvatx[$code];
							$objectdetfiscal->total_tva = $value;
							$objectdetfiscal->total_ht = $tvaht[$code];
							$objectdetfiscal->total_ttc = $tvattc[$code];
							$objectdetfiscal->amount_base = $pricebase;
							$objectdetfiscal->fk_user_create = $user->id;
							$objectdetfiscal->fk_user_mod = $user->id;
							$objectdetfiscal->date_create = dol_now();
							$objectdetfiscal->date_mod = dol_now();
							$objectdetfiscal->tms = dol_now();
							$objectdetfiscal->status = 1;
							$resf = $objectdetfiscal->create($user);
							if ($resf <=0)
							{
								$error++;
								setEventMessages($objectdetfiscal->error,$objectdetfiscal->errors,'errors');
								break;
							}
							if ($code == 'IVA')
							{
								$aTotaltav[$id]['tva_tx']	=$tvatx[$code];
								$aTotaltav[$id]['total_tva']	+=$value;
							}
							$aTotaltav[$id]['total_ht']	+=$tvaht[$code];
							$aTotaltav[$id]['total_ttc']	+=$tvattc[$code];
						}
							//agregamos a la tabla adicional objectdetadd
						$objectdetadd->fk_facture_fourn_det = $idligne;
						$objectdetadd->fk_object = $lines[$i]->rowid;
						$objectdetadd->object = $object_element;
						$objectdetadd->fk_fabrication = $lines[$i]->fk_fabrication;
						$objectdetadd->fk_fabricationdet = $lines[$i]->fk_fabricationdet;
						$objectdetadd->fk_projet = $lines[$i]->fk_projet;
						$objectdetadd->fk_projet_task = $lines[$i]->fk_projet_task;
						$objectdetadd->fk_jobs = $lines[$i]->fk_jobs;
						$objectdetadd->fk_jobsdet = $lines[$i]->fk_jobsdet;
						$objectdetadd->fk_structure = $lines[$i]->fk_structure;
						$objectdetadd->fk_poa = $lines[$i]->fk_poa;
						$objectdetadd->partida = $lines[$i]->partida;
						$objectdetadd->amount_ice = 0;
						$objectdetadd->discount = 0;
						$objectdetadd->fk_user_create = $user->id;
						$objectdetadd->fk_user_mod = $user->id;
						$objectdetadd->date_create = dol_now();
						$objectdetadd->date_mod = dol_now();
						$objectdetadd->tms = dol_now();
						$objectdetadd->status = 1;
						$resdadd = $objectdetadd->create($user);
						if ($resdadd<=0)
						{
							setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
							$error++;
						}
					}
						// Now reload line
					$object->fetch_lines();
				}
				else
				{
					$error++;
				}
			}
			else
			{
				$error++;
			}
				//actualizamos la cabecera
				//$objectdetfiscal->get_sum_taxes($id);

			$objtmp->fetch($id);
			$objtmp->tva = $aTotaltav[$id]['tva_tx']+0;
			$objtmp->total_tva = $aTotaltav[$id]['total_tva']+0;
			$objtmp->localtax1 = $aTotaltav[$id]['localtax1']+0;
			$objtmp->localtax2 = $aTotaltav[$id]['localtax2']+0;
			$objtmp->total_ht = $aTotaltav[$id]['total_ht']+0;
			$objtmp->total_ttc = $aTotaltav[$id]['total_ttc']+0;
			$resup = $objtmp->updatetot($user);
			if ($resup <= 0)
			{
				$error++;
				setEventMessages($objtmp->error,$objtmp->errors,'errors');

			}
			if (!$error && $element == 'finint')
			{
				$srcobject->fk_facture_fourn = $id;
				$res = $srcobject->update($user);
				if ($res <=0)
				{
					$error++;
					setEventMessages($srcobject->error,$srcobject->errors,'errors');
				}
			}
		}
		elseif (! $error)
		{
			$id = $object->create($user);
			if ($id < 0)
			{
				$error++;
			}

			if (! $error)
			{
					//agregamos a la tabla adicional
				$societe->fetch($object->socid);
				if ($societe->id == $object->socid)
					$objectadd->razsoc = $societe->name;

				$objectadd->fk_facture_fourn = $id;
				$objectadd->code_facture = GETPOST('code_facture');
				$objectadd->nit_company = GETPOST('nit_company');
				$objectadd->datec = dol_now();
				$objectadd->tms = dol_now();
				if ($lNit)
				{
					$objectadd->nit = GETPOST('nit');
					$objectadd->num_autoriz = GETPOST('num_autoriz');
					$objectadd->nfiscal = GETPOST('nfiscal');
					$objectadd->cod_control = GETPOST('cod_control');
				}
				else
				{
					$objectadd->nit = GETPOST('nit_');
					$objectadd->num_autoriz = GETPOST('num_autoriz_');
					$objectadd->nfiscal = GETPOST('nfiscal_');
					$objectadd->cod_control = GETPOST('cod_control_');
				}
				$objectadd->amount = 0;
				$objectadd->code_type_purchase = GETPOST('code_type_purchase');
				$objectadd->localtax3 = 0;
				$objectadd->localtax4 = 0;
				$objectadd->localtax5 = 0;
				$objectadd->localtax6 = 0;
				$objectadd->localtax7 = 0;
				$objectadd->localtax8 = 0;
				$objectadd->localtax9 = 0;

				$resadd = $objectadd->create($user);
				if ($resadd<=0)
				{
					setEventMessages($objectadd->error,$objectadd->errors,'errors');
					$error++;
				}
								// If some invoice's lines already known
				for ($i = 1 ; $i < 9 ; $i++)
				{
					$label = $_POST['label'.$i];
					$amountht  = price2num($_POST['amount'.$i]);
					$amountttc = price2num($_POST['amountttc'.$i]);
					$tauxtva   = price2num($_POST['tauxtva'.$i]);
					$qty = $_POST['qty'.$i];
					$fk_product = $_POST['fk_product'.$i];
					if ($label)
					{
						if ($amountht)
						{
							$price_base='HT'; $amount=$amountht;
						}
						else
						{
							$price_base='TTC'; $amount=$amountttc;
						}
						$atleastoneline=1;

						$product=new Product($db);
						$product->fetch($_POST['idprod'.$i]);

						$ret=$object->addline($label, $amount, $tauxtva, $product->localtax1_tx, $product->localtax2_tx, $qty, $fk_product, $remise_percent, '', '', '', 0, $price_base, $_POST['rang'.$i], 1);
						if ($ret < 0) $error++;
					}
				}
			}
		}

			//actualizamos en facture_fourn
		include DOL_DOCUMENT_ROOT.'/purchase/include/update_facture_fourn.inc.php';

			//fin acutalizacion cabecera
		if ($error)
		{
			$langs->load("errors");
			$db->rollback();

			setEventMessages($object->error, $object->errors, 'errors');
			$action='create';
			$_GET['socid']=$_POST['socid'];
		}
		else
		{
			$db->commit();
			if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
			{
				$outputlangs = $langs;
				$result = $object->generateDocument($object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
				if ($result	<= 0)
				{
					dol_print_error($db,$object->error,$object->errors);
					exit;
				}
			}
			header("Location: ".$_SERVER['PHP_SELF']."?id=".$id);
			exit;
		}
	}
	$action = 'create';
}
	// Edit line
if ($action == 'updateline' && $user->rights->fournisseur->facture->creer)
{
	$db->begin();
	$object->fetch($id);
	$object->fetch_thirdparty();
	$objectadd->fetch('',$object->id);
	//$objectdetadd->fetch(GETPOST('lineid'));
	$objectline = new SupplierInvoiceLine($db);
	$objectline->fetch(GETPOST('lineid'));
	$lines = new stdClass();
	$tva_tx = GETPOST('tva_tx');
	$qty = GETPOST('qty');
	if ($conf->global->PRICE_TAXES_INCLUDED)
	//if (GETPOST('price_ht') != '')
	{
		$pu = price2num(GETPOST('price_ttc'));
		$price_base_type = 'TTC';
	}
	else
	{
		$pu = price2num(GETPOST('price_ht'));
		$price_base_type = 'HT';
	}

	if (GETPOST('productid'))
	{
		$prod = new Product($db);
		$prod->fetch(GETPOST('productid'));
		$label = $prod->description;
		if (trim($_POST['product_desc']) != trim($label)) $label=$_POST['product_desc'];
		$type = $prod->type;
		$resadd = $productadd->fetch(0,GETPOST('productid'));
	}
	else
	{
		$label = $_POST['product_desc'];
		$type = $_POST["type"]?$_POST["type"]:0;
	}

	$date_start=dol_mktime(GETPOST('date_starthour'), GETPOST('date_startmin'), GETPOST('date_startsec'), GETPOST('date_startmonth'), GETPOST('date_startday'), GETPOST('date_startyear'));
	$date_end=dol_mktime(GETPOST('date_endhour'), GETPOST('date_endmin'), GETPOST('date_endsec'), GETPOST('date_endmonth'), GETPOST('date_endday'), GETPOST('date_endyear'));

	$localtax1_tx= get_localtax($_POST['tauxtva'], 1, $mysoc,$object->thirdparty);
	$localtax2_tx= get_localtax($_POST['tauxtva'], 2, $mysoc,$object->thirdparty);
	$remise_percent=GETPOST('remise_percent');

			// Extrafields Lines
	$extrafieldsline = new ExtraFields($db);
	$extralabelsline = $extrafieldsline->fetch_name_optionals_label($object->table_element_line);
	$array_options = $extrafieldsline->getOptionalsFromPost($extralabelsline);
			// Unset extrafield POST Data
	if (is_array($extralabelsline)) {
		foreach ($extralabelsline as $key => $value) {
			unset($_POST["options_" . $key]);
		}
	}
	$puttc = $up;
	//revisar tipo de impuestos
	$filter = array(1=>1);
	$filterstatic = " AND t.code_facture = '".trim($objectadd->code_facture)."'";
	$tvadef->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
	$tvaline = $tvadef->lines;
	//procesamos el calculo de los impuestos
	$tvacalc = array();
	$tvaht = array();
	$tvattc = array();
	$tvatx = array();
	$k = 1;

	$lines->fk_unit = $objectline->fk_unit;
	$lines->qty = GETPOST('qty');
	$lines->price = $pu;
	$lines->fk_product = GETPOST('productid');
	$amount_ice = GETPOST('amount_ice');
	if (empty($amount_ice)) $amount_ice = 0;
	$discount = GETPOST('discount')+0;
	if ($discount>0) $remise_percent = 0;
				if ($resadd > 0 && $productadd->sel_ice)
				{
					if ($amount_ice <=0)
					{
						$error++;
						setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Amountice")), 'errors');
					}
				}
				else $amount_ice = 0;
	include DOL_DOCUMENT_ROOT.'/fiscal/include/calclinefiscal.inc.php';

	$result=$object->updatelineadd(GETPOST('lineid'), $label, $lines->subprice, $lines->price, $lines->tva_tx, $lines->localtax1_tx, $lines->localtax2_tx, $lines->qty, $lines->fk_product, $price_base_type, 0, $type, $remise_percent, false,$lines);

		//registramos en tabla facture_fourn_det_add
	$idligne = GETPOST('lineid');
	foreach ((array) $tvacalc AS $code => $value)
	{
		if (!empty($code))
		{
			//buscamos si existe
			$filter = array(1=>1);
			$filterstatic = " AND t.fk_facture_fourn_det = ".$idligne;
			$filterstatic.= " AND t.code_tva = '".$code."'";
			$resfiscal = $objectdetfiscal->fetchAll('','',0,0,$filter,'AND',$filterstatic,true);
			$lAdd = false;
			if ($resfiscal<=0)
			{
				$lAdd = true;
				$objectdetfiscal->fk_facture_fourn_det = $idligne;
				$objectdetfiscal->fk_user_create = $user->id;
				$objectdetfiscal->fk_user_mod = $user->id;
				$objectdetfiscal->date_create = dol_now();
			}

			$objectdetfiscal->code_tva = $code;
			$objectdetfiscal->tva_tx = $tvatx[$code];
			$objectdetfiscal->total_tva = $value;
			$objectdetfiscal->total_ht = $tvaht[$code];
			$objectdetfiscal->total_ttc = $tvattc[$code];
			$objectdetfiscal->amount_base = $pricebase;
			$objectdetfiscal->fk_user_mod = $user->id;
			$objectdetfiscal->date_mod = dol_now();
			$objectdetfiscal->tms = dol_now();
			$objectdetfiscal->status = 1;
			if ($lAdd) $resf = $objectdetfiscal->create($user);
			else $resf = $objectdetfiscal->update($user);

			if ($resf<=0)
			{
				setEventMessages($objectdetfiscal->error, $objectdetfiscal->errors, 'errors');
				$error++;
			}
			if ($code == 'IVA')
			{
				$aTotaltav[$id]['tva_tx']	=$tvatx[$code];
				$aTotaltav[$id]['total_tva']	+=$value;
			}
			$aTotaltav[$id]['total_ht']	+=$tvaht[$code];
			$aTotaltav[$id]['total_ttc']	+=$tvattc[$code];
		}
	}
	if (!$error)
	{
			//buscamos o agregamos a la tabla adicional objectdetadd
		$resadd = $objectdetadd->fetch('',$idligne);
		$lAdd = false;
		if ($resadd<=0)
		{
			$lAdd = true;
			$objectdetadd->fk_user_create = $user->id;
			$objectdetadd->fk_user_mod = $user->id;
			$objectdetadd->date_create = dol_now();
		}
		$objectdetadd->fk_facture_fourn_det = $idligne;
		$objectdetadd->amount_ice = $amount_ice;
		$objectdetadd->discount = $discount;
		$objectdetadd->fk_user_mod = $user->id;
		$objectdetadd->date_mod = dol_now();
		$objectdetadd->tms = dol_now();
		$objectdetadd->status = 1;
		if ($lAdd) $resdadd = $objectdetadd->create($user);
		else $resdadd = $objectdetadd->update($user);
		if ($resdadd<=0)
		{
			setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
			$error++;
		}
	}

		//actualizamos en facture_fourn
	include DOL_DOCUMENT_ROOT.'/purchase/include/update_facture_fourn.inc.php';
	if (!$error)
	{
		unset($_POST['label']);
		unset($_POST['date_starthour']);
		unset($_POST['date_startmin']);
		unset($_POST['date_startsec']);
		unset($_POST['date_startday']);
		unset($_POST['date_startmonth']);
		unset($_POST['date_startyear']);
		unset($_POST['date_endhour']);
		unset($_POST['date_endmin']);
		unset($_POST['date_endsec']);
		unset($_POST['date_endday']);
		unset($_POST['date_endmonth']);
		unset($_POST['date_endyear']);
		unset($_POST['discount']);
		unset($_POST['amount_ice']);
		unset($_POST['price_ttc']);
		unset($_POST['price_ht']);
		unset($_POST['qty']);

		$db->commit();
	}
	else
	{
		$db->rollback();
		setEventMessages($object->error, $object->errors, 'errors');
	}
}

elseif ($action == 'addline' && $user->rights->fournisseur->facture->creer)
{
	$db->begin();

	$ret=$object->fetch($id);
	if ($ret < 0)
	{
		dol_print_error($db,$object->error);
		exit;
	}
	$ret1 = $objectadd->fetch('',$object->id);
	if ($ret1 < 0)
	{
		dol_print_error($db,$objectadd->error);
		exit;
	}
	$ret=$object->fetch_thirdparty();

	$langs->load('errors');
	$error=0;

		// Set if we used free entry or predefined product
	$predef='';
	$product_desc=(GETPOST('dp_desc')?GETPOST('dp_desc'):'');
	if (GETPOST('prod_entry_mode') == 'free')
	{
		$idprod=0;
		$price_ht = GETPOST('price_ht');
		$tva_tx = (GETPOST('tva_tx') ? GETPOST('tva_tx') : 0);
	}
	else
	{
		$idprod=GETPOST('idprod', 'int');
			//buscamos el producto
		if (empty($idprod))
		{
			if (!empty($conf->produit->enabled))
			{
				$product = new Product($db);
				$product->fetch($idprod,GETPOST('search_idprodfournprice'));
				if ($product->ref == GETPOST('search_idprodfournprice'))
					$idprod = $product->id;
				//buscamos en productadd
				$resadd = $productadd->fetch('',$idprod);
				if ($resadd < 0)
				{
					$error++;
					setEventMessages($productadd->error,$productadd->errors,'errors');
				}
			}
		}
		$price_ht = '';
		$tva_tx = '';
	}

	$qty = GETPOST('qty'.$predef);
	$remise_percent=GETPOST('remise_percent'.$predef);
	$discount = GETPOST('discount');
	$date_start=dol_mktime(GETPOST('date_start'.$predef.'hour'), GETPOST('date_start'.$predef.'min'), GETPOST('date_start' . $predef . 'sec'), GETPOST('date_start'.$predef.'month'), GETPOST('date_start'.$predef.'day'), GETPOST('date_start'.$predef.'year'));
	$date_end=dol_mktime(GETPOST('date_end'.$predef.'hour'), GETPOST('date_end'.$predef.'min'), GETPOST('date_end' . $predef . 'sec'), GETPOST('date_end'.$predef.'month'), GETPOST('date_end'.$predef.'day'), GETPOST('date_end'.$predef.'year'));

		// Extrafields
	$extrafieldsline = new ExtraFields($db);
	$extralabelsline = $extrafieldsline->fetch_name_optionals_label($object->table_element_line);
	$array_options = $extrafieldsline->getOptionalsFromPost($extralabelsline, $predef);
		// Unset extrafield
	if (is_array($extralabelsline)) {
			// Get extra fields
		foreach ($extralabelsline as $key => $value) {
			unset($_POST["options_" . $key]);
		}
	}

	if (GETPOST('prod_entry_mode')=='free' && GETPOST('price_ht') < 0 && $qty < 0)
	{
		setEventMessages($langs->trans('ErrorBothFieldCantBeNegative', $langs->transnoentitiesnoconv('UnitPrice'), $langs->transnoentitiesnoconv('Qty')), null, 'errors');
		$error++;
	}
	if (GETPOST('prod_entry_mode')=='free'  && ! GETPOST('idprodfournprice') && GETPOST('type') < 0)
	{
		setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Type')), null, 'errors');
		$error++;
	}
	if (GETPOST('prod_entry_mode')=='free' && GETPOST('price_ht')==='' && GETPOST('price_ttc')==='')
		// Unit price can be 0 but not ''
	{
		setEventMessages($langs->trans($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('UnitPrice'))), null, 'errors');
		$error++;
	}
	if (GETPOST('prod_entry_mode')=='free' && ! GETPOST('dp_desc'))
	{
		setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Description')), null, 'errors');
		$error++;
	}
	if (! GETPOST('qty'))
	{
		setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Qty')), null, 'errors');
		$error++;
	}

	if (GETPOST('prod_entry_mode') != 'free')
		// With combolist mode idprodfournprice is > 0 or -1. With autocomplete, idprodfournprice is > 0 or ''
	{
			//$idprod=0;
		$productsupplier=new ProductFournisseur($db);
		$fk_unit = 0;
			//buscamos el producto
		if (!empty($conf->produit->enabled))
		{
			$product = new Product($db);
			$product->fetch($idprod,(GETPOST('search_idprodfournprice')?GETPOST('search_idprodfournprice'):null));
			if ($product->ref == GETPOST('search_idprodfournprice'))
				$idprod = $product->id;
			$_POST['idprodfournprice'] = $idprod;
			$resadd = $productadd->fetch(0,$idprod);
			if ($resadd<0)
			{
				$error++;
				setEventMessages($productadd->error,$productadd->errors,'errors');
			}
			//vamos a validar la unidad de medida
			$fk_unit = $product->fk_unit;

		}

		if (GETPOST('idprodfournprice') == -1 || GETPOST('idprodfournprice') == '') $idprod=-2;
			// Same behaviour than with combolist. When not select idprodfournprice is now -2 (to avoid conflict with next action that may return -1)

		if (GETPOST('idprodfournprice') > 0)
		{
			//$idprod=$productsupplier->get_buyprice(GETPOST('idprodfournprice'), $qty);    // Just to see if a price exists for the quantity. Not used to found vat.
		}

			//registramos el producto fourniseur
		   //inicio agregar precio al producto prooveedor
		$id_fourn=$object->socid;

		$ref_fourn=dol_now();
		if (empty($ref_fourn)) $ref_fourn=GETPOST("search_ref_fourn");
		$quantity=1;
		if (empty($remise_percent)) $remise_percent = 0;
		if (empty($discount)) $discount = 0;

		$npr = preg_match('/\*/', $_POST['tva_tx']) ? 1 : 0 ;
		$tva_tx = str_replace('*','', GETPOST('tva_tx','alpha'));
		$tva_tx = price2num($tva_tx);

		if ($tva_tx == '')
		{
			$error++;
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("VATRateForSupplierProduct")), 'errors');
		}
		if (empty($ref_fourn))
		{
			$error++;
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("RefSupplier")), 'errors');
		}
		if ($id_fourn <= 0)
		{
			$error++;
			setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("Supplier")), 'errors');
		}
			//if ($ttc < 0)
			//{
			//    $error++;
			//    setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentities("Price")), 'errors');
			//}
		$product = new ProductFournisseur($db);
		$retarray = $product->list_product_fournisseur_price($idprod, '', '');
			//verificamos si tiene precios el proveedor
		$lProduct = false;
		if (empty($retarray)) $lProduct = true;
		foreach ((array) $retarray AS $k => $array)
		{
			if ($array->fk_fourn == $object->socid)
				$lProduct = true;
		}
		$result=$product->fetch($idprod);
		if ($result <= 0)
		{
			$error++;
			setEventMessage($product->error, 'errors');
		}
		if (! $error)
		{
				//agregamos producto al proveedor
			include DOL_DOCUMENT_ROOT.'/purchase/function/addproductfourn.php';
		}

			//fin registro producto fournisseur
			//Replaces $fk_unit with the product's
		if ($idprod > 0)
		{
				//definimos el tipo de impuesto
			$result=$productsupplier->fetch($idprod);

			$label = $productsupplier->label;

			$desc = $productsupplier->description;
			if (trim($product_desc) != trim($desc)) $desc = dol_concatdesc($desc, $product_desc);

			$tva_tx=get_default_tva($object->thirdparty, $mysoc, $productsupplier->id, $_POST['idprodfournprice']);
			$tva_npr = get_default_npr($object->thirdparty, $mysoc, $productsupplier->id, $_POST['idprodfournprice']);
			if (empty($tva_tx)) $tva_npr=0;
			$localtax1_tx= get_localtax($tva_tx, 1, $mysoc, $object->thirdparty, $tva_npr);
			$localtax2_tx= get_localtax($tva_tx, 2, $mysoc, $object->thirdparty, $tva_npr);

			$type = $productsupplier->type;

						//procesamos el calculo de los impuestos
			$tvacalc = array();
			$tvaht = array();
			$tvattc = array();
			$tvatx = array();

			$pu = GETPOST('price_ht');
			$price_base_type = 'HT';
			if ($conf->global->PRICE_TAXES_INCLUDED)
			{
				$price_base_type = 'TTC';
				$pu = GETPOST('price_ttc');
			}
			$k = 1;
			$lines = new stdClass();
			$lines->qty = $qty;
			$lines->price = $pu;
			$lines->fk_product = $idprod;
			$lines->fk_unit = GETPOST('fk_unit');
			if (!empty(GETPOST('fk_unit'))) $lines->fk_unit = GETPOST('fk_unit');
			elseif($productsupplier->fk_unit>0)
				$lines->fk_unit = $productsupplier->fk_unit;
			elseif ($fk_unit > 0)
				$lines->fk_unit = $fk_unit;

				$amount_ice = GETPOST('amount_ice');
				if ($productadd->sel_ice)
				{
					if ($amount_ice <=0)
					{
						$error++;
						setEventMessage($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Amountice")), 'errors');
					}
				}
				else $amount_ice = 0;

			include DOL_DOCUMENT_ROOT.'/fiscal/include/calclinefiscal.inc.php';

				//$result=$object->addline($desc, $productsupplier->fourn_pu, $tva_tx, $localtax1_tx, $localtax2_tx, $qty, $idprod, $remise_percent, $date_start, $date_end, 0, $tva_npr, $price_base_type, $type, -1, 0, $array_options, $productsupplier->fk_unit);

			$result = $object->addlineadd(
				$desc,
				$lines->subprice,
				$lines->price,
				$lines->tva_tx,
				$lines->localtax1_tx,
				$lines->localtax2_tx,
				$lines->qty,
				$lines->fk_product,
				$lines->remise_percent,
				$date_start,
				$date_end,
				0,
				$lines->info_bits,
				$price_base_type,
				$type,
				-1,
				false,
				$lines
			);

			if ($result < 0)
			{
				setEventMessages($langs->trans('Error de addline').' '.$result, null, 'errors');
				$error++;
			}
				//registramos en tabla facture_fourn_det_add
			$idligne = $object->rowid;
			foreach ((array) $tvacalc AS $code => $value)
			{
				if (!empty($code))
				{
					$objectdetfiscal->fk_facture_fourn_det = $idligne;
					$objectdetfiscal->code_tva = $code;
					$objectdetfiscal->tva_tx = $tvatx[$code];
					$objectdetfiscal->total_tva = $value;
					$objectdetfiscal->total_ht = $tvaht[$code];
					$objectdetfiscal->total_ttc = $tvattc[$code];
					$objectdetfiscal->amount_base = $pricebase;
					$objectdetfiscal->discount = $discount;
					$objectdetfiscal->fk_user_create = $user->id;
					$objectdetfiscal->fk_user_mod = $user->id;
					$objectdetfiscal->date_create = dol_now();
					$objectdetfiscal->date_mod = dol_now();
					$objectdetfiscal->tms = dol_now();
					$objectdetfiscal->status = 1;
					$resf = $objectdetfiscal->create($user);
					if ($resf<=0)
					{
						setEventMessages($objectdetfiscal->error, $objectdetfiscal->errors, 'errors');
						$error++;
					}
					if ($code == 'IVA')
					{
						$aTotaltav[$id]['tva_tx']	=$tvatx[$code];
						$aTotaltav[$id]['total_tva']	+=$value;
					}
					$aTotaltav[$id]['total_ht']	+=$tvaht[$code];
					$aTotaltav[$id]['total_ttc']	+=$tvattc[$code];
				}
			}
				//agregamos a la tabla adicional objectdetadd
			$objectdetadd->fk_facture_fourn_det = $idligne;
			$objectdetadd->amount_ice = $amount_ice;
			$objectdetadd->discount = $discount;
			$objectdetadd->fk_user_create = $user->id;
			$objectdetadd->fk_user_mod = $user->id;
			$objectdetadd->date_create = dol_now();
			$objectdetadd->date_mod = dol_now();
			$objectdetadd->tms = dol_now();
			$objectdetadd->status = 1;
			$resdadd = $objectdetadd->create($user);
			if ($resdadd<=0)
			{
				setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
				$error++;
			}

				//actualizamos en facture_fourn
			include DOL_DOCUMENT_ROOT.'/purchase/include/update_facture_fourn.inc.php';

				//$price_base_type = 'HT';

				// TODO Save the product supplier ref into database into field ref_supplier (must rename field ref into ref_supplier first)
		}
		if ($idprod == -2 || $idprod == 0)
		{
				// Product not selected
			$error++;
			$langs->load("errors");
			setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("ProductOrService")), null, 'errors');
		}
		if ($idprod == -1)
		{
			// Quantity too low
			$error++;
			$langs->load("errors");
			setEventMessages($langs->trans("ErrorQtyTooLowForThisSupplier"), null, 'errors');
		}
	}
	else if( GETPOST('price_ht')!=='' || GETPOST('price_ttc')!=='' )
	{
		$pu_ht = price2num($price_ht, 'MU');
		$pu_ttc = price2num(GETPOST('price_ttc'), 'MU');
		$tva_npr = (preg_match('/\*/', $tva_tx) ? 1 : 0);
		$tva_tx = str_replace('*', '', $tva_tx);
		$label = (GETPOST('product_label') ? GETPOST('product_label') : '');
		$desc = $product_desc;
		$type = GETPOST('type');

		$fk_unit= GETPOST('units', 'alpha');

		$tva_tx = price2num($tva_tx);
			// When vat is text input field

			// Local Taxes
		$localtax1_tx= get_localtax($tva_tx, 1,$mysoc,$object->thirdparty);
		$localtax2_tx= get_localtax($tva_tx, 2,$mysoc,$object->thirdparty);

		if (!empty($_POST['price_ht']))
		{
			$ht = price2num($_POST['price_ht']);
			$price_base_type = 'HT';
		}
		else
		{
			$ttc = price2num($_POST['price_ttc']);
			$ht = $ttc / (1 + ($tva_tx / 100));
			$price_base_type = 'HT';
		}

		$price_base_type = 'HT';
		if ($conf->global->PRICE_TAXES_INCLUDED)
		{
			$price_base_type = 'TTC';
			$pu_ttc = GETPOST('price_ttc');
			$pu = $pu_ttc;
				//$ht = $ttc / (1 + ($tva_tx / 100));
			$ht = $pu_ttc;
		}
		else
		{
			$price_base_type = 'HT';
			$ht = GETPOST('price_ht');
			$pu = $ht;
			$pu_ttc = $ht;
		}

		$k = 1;
		$lines = new stdClass();
		$lines->qty = $qty;
		$lines->price = $pu;
		$lines->fk_product = $idprod;
		$lines->fk_unit = GETPOST('fk_unit');
		if (!empty(GETPOST('fk_unit'))) $lines->fk_unit = GETPOST('fk_unit');
		else $lines->fk_unit = $productsupplier->fk_unit;
		$amount_ice = 0;

		include DOL_DOCUMENT_ROOT.'/fiscal/include/calclinefiscal.inc.php';

			//$result=$object->addline($product_desc, $ht, $tva_tx, $localtax1_tx, $localtax2_tx, $qty, 0, $remise_percent, $date_start, $date_end, 0, $tva_npr, $price_base_type, $type, -1, 0, $array_options, $fk_unit);
			//vamos a redondear el tema de impuestos
			//para que la contabilidad tenga un registro exacto
		$lines->total_localtax1 = price2num($lines->total_localtax1,'MT');
		$lines->total_localtax2 = price2num($lines->total_localtax2,'MT');
		$result = $object->addlineadd(
			$desc,
			$lines->subprice,
			$lines->price,
			$lines->tva_tx,
			$lines->localtax1_tx,
			$lines->localtax2_tx,
			$lines->qty,
			$lines->fk_product,
			$lines->remise_percent,
			$date_start,
			$date_end,
			0,
			$lines->info_bits,
			$price_base_type,
			$type,
			-1,
			false,
			$lines
		);


		if ($result < 0)
		{
			setEventMessages($langs->trans('Error de addline').' '.$result, null, 'errors');
			$error++;
		}
			//registramos en tabla facture_fourn_det_add
		$idligne = $object->rowid;

		foreach ((array) $tvacalc AS $code => $value)
		{
			if (!empty($code))
			{
				$objectdetfiscal->fk_facture_fourn_det = $idligne;
				$objectdetfiscal->code_tva = $code;
				$objectdetfiscal->tva_tx = $tvatx[$code];
				$objectdetfiscal->total_tva = $value;
				$objectdetfiscal->total_ht = $tvaht[$code];
				$objectdetfiscal->total_ttc = $tvattc[$code];
				$objectdetfiscal->amount_base = $pricebase;
				$objectdetfiscal->fk_user_create = $user->id;
				$objectdetfiscal->fk_user_mod = $user->id;
				$objectdetfiscal->date_create = dol_now();
				$objectdetfiscal->date_mod = dol_now();
				$objectdetfiscal->tms = dol_now();
				$objectdetfiscal->status = 1;
				$resf = $objectdetfiscal->create($user);
				if ($resf<=0)
				{
					setEventMessages($objectdetfiscal->error, $objectdetfiscal->errors, 'errors');
					$error++;
				}
				if ($code == 'IVA')
				{
					$aTotaltav[$id]['tva_tx']	=$tvatx[$code];
					$aTotaltav[$id]['total_tva']	+=$value;
				}
				$aTotaltav[$id]['total_ht']	+=$tvaht[$code];
				$aTotaltav[$id]['total_ttc']	+=$tvattc[$code];
			}
		}
				//agregamos a la tabla adicional objectdetadd
		$objectdetadd->fk_facture_fourn_det = $idligne;
		$objectdetadd->amount_ice = $amount_ice+0;
		$objectdetadd->discount = $discount;
		if (empty($objectdetadd->discount)) $objectdetadd->discount=0;
		$objectdetadd->fk_user_create = $user->id;
		$objectdetadd->fk_user_mod = $user->id;
		$objectdetadd->date_create = dol_now();
		$objectdetadd->date_mod = dol_now();
		$objectdetadd->tms = dol_now();
		$objectdetadd->status = 1;
		$resdadd = $objectdetadd->create($user);
		if ($resdadd<=0)
		{
			setEventMessages($objectdetadd->error,$objectdetadd->errors,'errors');
			$error++;
		}
			//actualizamos en facture_fourn
		include DOL_DOCUMENT_ROOT.'/purchase/include/update_facture_fourn.inc.php';
	}

		//print "xx".$tva_tx; exit;
	if (! $error && $result > 0)
	{
		$db->commit();
		setEventMessages($langs->trans('Registro guardado'), null, 'mesgs');

			// Define output language
		if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
		{
			$outputlangs = $langs;
			$newlang = '';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
			if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
			if (! empty($newlang)) {
				$outputlangs = new Translate("", $conf);
				$outputlangs->setDefaultLang($newlang);
			}
			$model=$object->modelpdf;
			$ret = $object->fetch($id);
				// Reload to get new records

			$result=$object->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
			if ($result < 0) dol_print_error($db,$result);
		}

		unset($_POST ['prod_entry_mode']);

		unset($_POST['qty']);
		unset($_POST['type']);
		unset($_POST['remise_percent']);
		unset($_POST['pu']);
		unset($_POST['price_ht']);
		unset($_POST['multicurrency_price_ht']);
		unset($_POST['price_ttc']);
		unset($_POST['tva_tx']);
		unset($_POST['label']);
		unset($localtax1_tx);
		unset($localtax2_tx);
		unset($_POST['np_marginRate']);
		unset($_POST['np_markRate']);
		unset($_POST['dp_desc']);
		unset($_POST['idprodfournprice']);
		unset($_POST['units']);
		unset($_POST['discount']);

		unset($_POST['date_starthour']);
		unset($_POST['date_startmin']);
		unset($_POST['date_startsec']);
		unset($_POST['date_startday']);
		unset($_POST['date_startmonth']);
		unset($_POST['date_startyear']);
		unset($_POST['date_endhour']);
		unset($_POST['date_endmin']);
		unset($_POST['date_endsec']);
		unset($_POST['date_endday']);
		unset($_POST['date_endmonth']);
		unset($_POST['date_endyear']);
	}
	else
	{
		$db->rollback();
		setEventMessages($object->error, $object->errors, 'errors');
	}

	$action = '';
}

elseif ($action == 'classin')
{
	$object->fetch($id);
	$result=$object->setProject($projectid);
}


	// Set invoice to draft status
elseif ($action == 'edit' && $user->rights->fournisseur->facture->creer)
{
	$object->fetch($id);

	$totalpaye = $object->getSommePaiement();
	$resteapayer = $object->total_ttc - $totalpaye;

		// On verifie si les lignes de factures ont ete exportees en compta et/ou ventilees
		//$ventilExportCompta = $object->getVentilExportCompta();

		// On verifie si aucun paiement n'a ete effectue
	if ($resteapayer == $object->total_ttc	&& $object->paye == 0 && $ventilExportCompta == 0)
	{
		$object->set_draft($user);

			// Define output language
		if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
		{
			$outputlangs = $langs;
			$newlang = '';
			if ($conf->global->MAIN_MULTILANGS && empty($newlang) && GETPOST('lang_id')) $newlang = GETPOST('lang_id','alpha');
			if ($conf->global->MAIN_MULTILANGS && empty($newlang))	$newlang = $object->thirdparty->default_lang;
			if (! empty($newlang)) {
				$outputlangs = new Translate("", $conf);
				$outputlangs->setDefaultLang($newlang);
			}
			$model=$object->modelpdf;
			$ret = $object->fetch($id);
				// Reload to get new records

			$result=$object->generateDocument($model, $outputlangs, $hidedetails, $hidedesc, $hideref);
			if ($result < 0) dol_print_error($db,$result);
		}

		$action='';
	}
}

	// Set invoice to validated/unpaid status
elseif ($action == 'reopen' && $user->rights->fournisseur->facture->creer)
{
	$result = $object->fetch($id);
	if ($object->statut == FactureFournisseur::STATUS_CLOSED
		|| ($object->statut == FactureFournisseur::STATUS_ABANDONED && $object->close_code != 'replaced'))
	{
		$result = $object->set_unpaid($user);
		if ($result > 0)
		{
			header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id);
			exit;
		}
		else
		{
			setEventMessages($object->error, $object->errors, 'errors');
		}
	}
}

	/*
	 * Send mail
	 */

	// Actions to send emails
	$actiontypecode='AC_SUP_INV';
	$trigger_name='BILL_SUPPLIER_SENTBYMAIL';
	$paramname='id';
	$mode='emailfromsupplierinvoice';
	include DOL_DOCUMENT_ROOT.'/core/actions_sendmails.inc.php';


	// Build document
	if ($action == 'builddoc')
	{
		// Save modele used
		$object->fetch($id);
		$object->fetch_thirdparty();

		// Save last template used to generate document
		if (GETPOST('model')) $object->setDocModel($user, GETPOST('model','alpha'));

		$outputlangs = $langs;
		$newlang=GETPOST('lang_id','alpha');
		if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$object->thirdparty->default_lang;
		if (! empty($newlang))
		{
			$outputlangs = new Translate("",$conf);
			$outputlangs->setDefaultLang($newlang);
		}
		$result = $object->generateDocument($object->modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
		if ($result	<= 0)
		{
			setEventMessages($object->error, $object->errors, 'errors');
			$action='';
		}
	}
	// Make calculation according to calculationrule
	if ($action == 'calculate')
	{
		$calculationrule=GETPOST('calculationrule');

		$object->fetch($id);
		$object->fetch_thirdparty();
		$result=$object->update_price(0, (($calculationrule=='totalofround')?'0':'1'), 0, $object->thirdparty);
		if ($result	<= 0)
		{
			dol_print_error($db,$result);
			exit;
		}
	}
	// Delete file in doc form
	if ($action == 'remove_file')
	{
		require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

		if ($object->fetch($id))
		{
			$object->fetch_thirdparty();
			$upload_dir =	$conf->fournisseur->facture->dir_output . "/";
			$file =	$upload_dir	. '/' .	GETPOST('file');
			$ret=dol_delete_file($file,0,0,0,$object);
			if ($ret) setEventMessages($langs->trans("FileWasRemoved", GETPOST('urlfile')), null, 'mesgs');
			else setEventMessages($langs->trans("ErrorFailToDeleteFile", GETPOST('urlfile')), null, 'errors');
		}
	}

	if ($action == 'update_extras')
	{
		// Fill array 'array_options' with data from add form
		$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);
		$ret = $extrafields->setOptionalsFromPost($extralabels,$object,GETPOST('attribute'));
		if ($ret < 0) $error++;

		if (!$error)
		{
			// Actions on extra fields (by external module or standard code)
			// TODO le hook fait double emploi avec le trigger !!
			$hookmanager->initHooks(array('supplierinvoicedao'));
			$parameters=array('id'=>$object->id);

			$reshook=$hookmanager->executeHooks('insertExtraFields',$parameters,$object,$action);
			// Note that $action and $object may have been modified by some hooks

			if (empty($reshook))
			{
				if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED))
				// For avoid conflicts if trigger used
				{

					$result=$object->insertExtraFields();

					if ($result < 0)
					{
						$error++;
					}

				}
			}
			else if ($reshook < 0) $error++;
		}
		else
		{
			$action = 'edit_extras';
		}
	}

	if (! empty($conf->global->MAIN_DISABLE_CONTACTS_TAB) && $user->rights->fournisseur->facture->creer)
	{
		if ($action == 'addcontact')
		{
			$result = $object->fetch($id);

			if ($result > 0 && $id > 0)
			{
				$contactid = (GETPOST('userid') ? GETPOST('userid') : GETPOST('contactid'));
				$result = $object->add_contact($contactid, $_POST["type"], $_POST["source"]);
			}

			if ($result >= 0)
			{
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$object->id);
				exit;
			}
			else
			{
				if ($object->error == 'DB_ERROR_RECORD_ALREADY_EXISTS')
				{
					$langs->load("errors");
					setEventMessages($langs->trans("ErrorThisContactIsAlreadyDefinedAsThisType"), null, 'errors');
				}
				else
				{
					setEventMessages($object->error, $object->errors, 'errors');
				}
			}
		}

		// bascule du statut d'un contact
		else if ($action == 'swapstatut')
		{
			if ($object->fetch($id))
			{
				$result=$object->swapContactStatus(GETPOST('ligne'));
			}
			else
			{
				dol_print_error($db);
			}
		}

		// Efface un contact
		else if ($action == 'deletecontact')
		{
			$object->fetch($id);
			$result = $object->delete_contact($_GET["lineid"]);

			if ($result >= 0)
			{
				header("Location: ".$_SERVER['PHP_SELF']."?id=".$object->id);
				exit;
			}
			else {
				dol_print_error($db);
			}
		}
	}
	?>