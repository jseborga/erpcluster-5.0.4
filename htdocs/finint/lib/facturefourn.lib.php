<?php
//crea registro de factura a proveedor
//registra el pago via banco o caja

function facturefourn($data)
{
	global $langs,$conf,$user,$db, $objaccount;

	$origin 		= $data['origin'];
	$originid 		= $data['originid'];
	$fk_soc      	= $data['fk_soc']+0;
	$ref_soc     	= $data['ref_soc'];
	$type        	= $data['type']+0;
	//por defecto el tipo es 0
	$datefacture 	= $data['datefacture'];
	$datedue     	= $data['datedue'];
	$fk_projet   	= $data['fk_projet'];
	$fk_projet_task	= $data['fk_projet_task'];
	$fk_account  	= $data['fk_account'];
	$label       	= $data['label'];
	$closepaidinvoices = $data['closepaidinvoices'];
	$mode_reglement_id = $data['mode_reglement'];
	$code_facture 		= $data['code_facture'];
	$code_type_purchase = $data['code_type_purchase'];
	$nit_company 		= $data['nit_company'];
	$type_operation 	= $data['type_operation'];

	//datos de factura
	$codeqr = $data['codeqr'];
	$fourn_nit = $data['fourn_nit'];
	$fourn_soc = $data['fourn_soc'];
	$fourn_facture = $data['fourn_facture'];
	$fourn_numaut = $data['fourn_numaut'];
	$fourn_date = $data['fourn_date'];

	//buscamos la cuenta
	$objaccount->fetch($fk_account);
	if ($objaccount->courant == 2)
	{
		$data['operation']='LIQ';
	}


	$objpaiement = get_c_paiement('',$data['operation']);
	//para pago
	if ($objpaiement->id)
		$paiementid   = $objpaiement->id;
	else
	{
		if ($data['operation'] == 'LIQ')
			$paiementid = 4;
	}

	$num_paiement = $data['num_paiement'];
	$comment      = $data['comment'];
	$closepaidinvoice = $data['closepaidinvoice'];
	//para cerrar la factura como pagada debe ser on

	//fijos
	$cond_reglement_id = 1;
	//registro de los productos
	$aProduct = $data['aProduct'];

	//llamamos a las clases para almacenar
	require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/modules/supplier_invoice/modules_facturefournisseur.php';
	require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
	require_once DOL_DOCUMENT_ROOT.'/fourn/class/paiementfourn.class.php';
	require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
	if ($conf->purchase->enabled)
	{
		require_once DOL_DOCUMENT_ROOT.'/purchase/class/fournisseur.factureext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/purchase/class/facturefournadd.class.php';
		require_once DOL_DOCUMENT_ROOT.'/purchase/class/facturefourndetadd.class.php';
	}

	if ($conf->fiscal->enabled)
	{
		require_once DOL_DOCUMENT_ROOT.'/fiscal/class/tvadefadd.class.php';
		require_once DOL_DOCUMENT_ROOT.'/fiscal/class/productadd.class.php';
		require_once DOL_DOCUMENT_ROOT.'/fiscal/lib/fiscal.lib.php';
		require_once DOL_DOCUMENT_ROOT.'/fiscal/class/entity.class.php';
		require_once DOL_DOCUMENT_ROOT.'/fiscal/class/entityaddext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/fiscal/class/ctypefacture.class.php';
		//
		require_once DOL_DOCUMENT_ROOT.'/fiscal/class/facturefourndetfiscalext.class.php';
	}

	if ($conf->produit->enabled)
		require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
	if ($conf->projet->enabled)
	{
		require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
		require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
	}
	$object = new FactureFournisseurext($db);
	$objtmp = new FactureFournisseurext($db);

	$extrafields = new ExtraFields($db);

	// fetch optionals attributes and labels
	$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);

	$societe = new Societe($db);
	$objectdetfiscal = new Facturefourndetfiscalext($db);
	$objectdetadd 	= new Facturefourndetadd($db);
	$objectadd 		= new Facturefournadd($db);
	$objtmpadd 		= new Facturefournadd($db);
	$tvadef 		= new Tvadefadd($db);

	//procedemos al registro
	$error=0;

	if (! $error)
	{
		//$db->begin();

		$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);
		$ret = $extrafields->setOptionalsFromPost($extralabels, $object);
		if ($ret < 0) $error++;

		$tmpproject = $fk_projet;

			// Creation facture
		$code=generarcodigo(6);
		$object->ref           = $ref.$code;
		$object->ref_supplier  = $ref_soc;
		$object->socid         = $fk_soc;
		$object->libelle       = ($label?$label:$data['detail']);
		$object->date          = $datefacture;
		$object->date_echeance = $datedue;
		$object->note_public   = GETPOST('note_public');
		$object->note_private  = GETPOST('note_private');
		$object->cond_reglement_id = $cond_reglement_id;
		$object->mode_reglement_id = $mode_reglement_id;
		$object->fk_account        = $fk_account;
		$object->fk_project    = ($tmpproject > 0) ? $tmpproject : null;
		//$object->fk_incoterms = GETPOST('incoterm_id', 'int');
		//$object->location_incoterms = GETPOST('location_incoterms', 'alpha');
		//$object->multicurrency_code = GETPOST('multicurrency_code', 'alpha');
		//$object->multicurrency_tx = GETPOST('originmulticurrency_tx', 'int');

			// Auto calculation of date due if not filled by user
		if(empty($object->date_echeance)) $object->date_echeance = $object->calculate_date_lim_reglement();

		//echo $error.' '.$_POST['origin'].' '.$_POST['originid'];exit;
			// If creation from another object of another module
		if (! $error && $origin && $originid)
		{
				// Parse element/subelement (ex: project_task)
			$element = $subelement = $origin;
				/*if (preg_match('/^([^_]+)_([^_]+)/i',$_POST['origin'],$regs))
				 {
				$element = $regs[1];
				$subelement = $regs[2];
			}*/
			$object_element = $element;
				// For compatibility
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
			}
			if ($element == 'requestcashdeplacement') {
				$element = 'finint'; $subelement = 'requestcashdeplacementext';
			}
			if ($element == 'project')
			{
				$element = 'projet';
			}
			$object->origin    = $origin;
			$object->origin_id = $originid;

			$id = $object->create($user);

				// Add lines
			if ($id > 0)
			{
				//agregamos a la tabla adicional
				$societe->fetch($object->socid);
				if ($societe->id == $object->socid) $objectadd->razsoc = $societe->name;
				$objectadd->fk_facture_fourn = $id;
				$objectadd->nit_company = $nit_company;
				$objectadd->code_facture = $code_facture;
				$objectadd->code_type_purchase = $code_type_purchase;
				$objectadd->fk_projet_task = $fk_projet_task+0;
				$objectadd->datec = dol_now();
				$objectadd->tms = dol_now();
				$objectadd->amount = 0;
				$objectadd->ndui = $ndui;

				//registro de la factura
				$objectadd->nit = $fourn_nit;
				$objectadd->num_autoriz = $fourn_numaut;
				$objectadd->nfiscal = $fourn_facture+0;
				$objectadd->cod_control = $fourn_cod_control;

				$objectadd->localtax3 = 0;
				$objectadd->localtax4 = 0;
				$objectadd->localtax5 = 0;
				$objectadd->localtax6 = 0;
				$objectadd->localtax7 = 0;
				$objectadd->localtax8 = 0;
				$objectadd->localtax9 = 0;
				$resadd = $objectadd->create($user);
				if ($resadd <= 0)
				{
					$error++;
					setEventMessages($objectadd->error,$objectadd->errors,'errors');
				}



				require_once DOL_DOCUMENT_ROOT.'/'.$element.'/class/'.$subelement.'.class.php';
				$classname = ucfirst($subelement);
				if ($classname == 'Fournisseur.commande') $classname='CommandeFournisseur';
				$srcobject = new $classname($db);

				$result=$srcobject->fetch($originid);
				if ($result > 0)
				{
					$lines = $srcobject->lines;
					if (empty($lines) && method_exists($srcobject,'fetch_lines'))
					{
						$srcobject->fetch_lines();
						$lines = $srcobject->lines;
					}

					$num=count($lines);
					for ($i = 0; $i < $num; $i++)
					{
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

						if ($conf->fiscal->enabled)
						{
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
								echo '<hr>senevia como neto ';
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
						else
						{
							// FIXME Missing $lines[$i]->ref_supplier and $lines[$i]->label into addline and updateline methods. They are filled when coming from order for example.

							$result = $object->addline(
								$desc,
								$lines[$i]->subprice,
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
								'HT',
								$product_type,
								$lines[$i]->rang,
								0,
								$lines[$i]->array_options,
								$lines[$i]->fk_unit
								);
						}
						if ($result < 0)
						{
							$error++;
							break;
						}
					}

						// Now reload line
					$object->fetch_lines();
				}
				else
				{
					$error++;
					setEventMessages($srcobject->error,$srcobject->errors,'errors');
				}
			}
			else
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
				//actualizamos la cabecera
				//$objectdetfiscal->get_sum_taxes($id);
			//actualizamos en facture_fourn
			//$objtmp = new FactureFournisseur($db);
			$restmp = $objtmp->fetch($id);
			if ($restmp <=0)
			{
				setEventMessages($objtmp->error, $objtmp->errors, 'errors');
			}
			$objtmpadd->fetch ('',$id);

			//recuperamos la suma total para actualizar en cabecera
			$objectdetfiscal->get_sum_taxes($id);
			$x = 1;
			foreach ((array) $objectdetfiscal->aData AS $code_tva => $data)
			{
				if ($code_tva == 'IVA')
				{
					$objtmp->tva = $data['tva_tx'];
					$objtmp->total_tva = $data['total_tva'];
				}
				else
				{
					$campo = 'localtax'.$x;
					$objtmp->$campo = $data['total_tva'];
					$objtmpadd->$campo = $data['total_tva'];
					$x++;
				}
				$objtmp->total_ht = $data['total_ht'];
				$objtmp->total_ttc = $data['total_ttc'];
				$objtmpadd->amountfiscal = $data['total_amountfiscal']+0;
				$objtmpadd->amountnofiscal = $data['total_amountnofiscal']+0;
				$objtmpadd->amount_ice = $data['total_amountice'];
				$objtmpadd->amount_ice = $data['total_amountice'];
			}
			//recuperamos de objectdetadd la suma de descuento y ice
			$filter = array(1=>1);
			$ids = implode(',',$objectdetfiscal->aDataid);
			$filterstatic = " AND t.fk_facture_fourn_det IN (".$ids.")";
			$resdadd = $objectdetadd->fetchAll('','',0,0,$filter,'AND',$filterstatic);
			foreach ((array) $objectdetadd->lines AS $j => $line)
			{
				$objtmp->total_ttc+=$line->amount_ice;
				$objtmp->remise+=$line->discount;
				$objtmpadd->total_ttc+=$line->amount_ice;
				$objtmpadd->discount+=$line->discount;
			}
			if ($objtmp->total_ttc == 0)
			{
				$objtmp->fetch($id);
				$objtmpadd->fetch ('',$id);
				//recorremos de object->lines
				foreach ((array) $object->lines AS $j => $line)
				{
					$objtmp->total_ttc+=$line->total_ttc;
					$objtmp->total_ht+=$line->total_ht;
					$objtmpadd->total_ttc+=$line->total_ttc;
					$objtmpadd->total_ht+=$line->total_ht;
				}
			}
			$resup = $objtmp->updatetot($user);

			$amounts[$id] += price2num($objtmp->total_ttc,'MU');

			if ($resup <=0)
			{
				setEventMessages($langs->trans('Error de updatetot'), null, 'errors');
				$error++;
			}
			$resupadd = $objtmpadd->update($user);
			if ($resupadd <=0)
			{
				setEventMessages($langs->trans('Error de resupaddx'), null, 'errors');
				$error++;
			}



		}
		elseif (! $error)
		{
			$id = $object->create($user);
			if ($id < 0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}

			if (! $error)
			{
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

						if ($ret < 0) $error++;
						$ret=$object->addline($label, $amount, $tauxtva, $product->localtax1_tx, $product->localtax2_tx, $qty, $fk_product, $remise_percent, '', '', '', 0, $price_base, $_POST['rang'.$i], 1);
					}
				}
			}
		}
		//procedemos a registrar los productos
		if ($id>0 && $abc)
		{
			foreach ((array) $aProduct AS $j =>$row)
			{
				//contenido de $row
				$dp_desc = $row['dp_desc'];
				$product_desc = $row['dp_desc'];
				$prod_entry_mode = $row['prod_entry_mode'];
				$price_ht = $row['price_ht'];
				$price_ttc = $row['price_ttc'];
				$tva_tx = $row['tva_tx'];
				$idprod = $row['idprod'];
				$qty = $row['qty'];
				$remise_percent = $row['remise_percent'];
				$date_start = $row['date_start'];
				$date_end = $row['date_end'];
				$idprodfournprice = $row['idprodfournprice'];
				$type = $row['type'];
				$fk_unit = $row['fk_unit'];
				// 0 = producto
				// 1 = servicio
				//$db->begin();

				$ret=$object->fetch($id);
				if ($ret < 0)
				{
					dol_print_error($db,$object->error);
					exit;
				}
				$ret=$object->fetch_thirdparty();

				$langs->load('errors');

				// Set if we used free entry or predefined product
				$predef='';

				//$product_desc=(GETPOST('dp_desc')?GETPOST('dp_desc'):'');
				//if (GETPOST('prod_entry_mode') == 'free')

				if ($prod_entry_mode == 'free')
				{
					$idprod=0;

					//$price_ht = GETPOST('price_ht');
					//$tva_tx = (GETPOST('tva_tx') ? GETPOST('tva_tx') : 0);
				}
				else
				{
					//$idprod=GETPOST('idprod', 'int');
					$price_ht = '';
					$tva_tx = '';
				}

				//$qty = GETPOST('qty'.$predef);
				//$remise_percent=GETPOST('remise_percent'.$predef);

				//$date_start=dol_mktime(GETPOST('date_start'.$predef.'hour'), GETPOST('date_start'.$predef.'min'), GETPOST('date_start' . $predef . 'sec'), GETPOST('date_start'.$predef.'month'), GETPOST('date_start'.$predef.'day'), GETPOST('date_start'.$predef.'year'));
				//$date_end=dol_mktime(GETPOST('date_end'.$predef.'hour'), GETPOST('date_end'.$predef.'min'), GETPOST('date_end' . $predef . 'sec'), GETPOST('date_end'.$predef.'month'), GETPOST('date_end'.$predef.'day'), GETPOST('date_end'.$predef.'year'));

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
				//if (GETPOST('prod_entry_mode')=='free' && GETPOST('price_ht') < 0 && $qty < 0)
				if ($prod_entry_mode=='free' && $price_ht < 0 && $qty < 0)
				{
					setEventMessages($langs->trans('ErrorBothFieldCantBeNegative', $langs->transnoentitiesnoconv('UnitPrice'), $langs->transnoentitiesnoconv('Qty')), null, 'errors');
					$error++;
				}
				//if (GETPOST('prod_entry_mode')=='free'  && ! GETPOST('idprodfournprice') && GETPOST('type') < 0)
				if ($prod_entry_mode=='free'  && ! $idprodfournprice && $type < 0)
				{
					setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Type')), null, 'errors');
					$error++;
				}
				//if (GETPOST('prod_entry_mode')=='free' && GETPOST('price_ht')==='' && GETPOST('price_ttc')==='')
				// Unit price can be 0 but not ''
				if ($prod_entry_mode=='free' && $price_ht==='' && $price_ttc==='')

				{
					setEventMessages($langs->trans($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('UnitPrice'))), null, 'errors');
					$error++;
				}
				//if (GETPOST('prod_entry_mode')=='free' && ! GETPOST('dp_desc'))
				if ($prod_entry_mode=='free' && ! $dp_desc)

				{
					setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Description')), null, 'errors');
					$error++;
				}
				//if (! GETPOST('qty'))
				if (! $qty)
				{
					setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Qty')), null, 'errors');
					$error++;
				}
				//if (GETPOST('prod_entry_mode') != 'free')
				if ($prod_entry_mode != 'free')
				// With combolist mode idprodfournprice is > 0 or -1. With autocomplete, idprodfournprice is > 0 or ''
				{
					$idprod=0;
					$productsupplier=new ProductFournisseur($db);

					//if (GETPOST('idprodfournprice') == -1 || GETPOST('idprodfournprice') == '') $idprod=-2;
					if ($idprodfournprice == -1 || $idprodfournprice == '') $idprod=-2;
					// Same behaviour than with combolist. When not select idprodfournprice is now -2 (to avoid conflict with next action that may return -1)

					//if (GETPOST('idprodfournprice') > 0)
					if ($idprodfournprice > 0)
					{
						//$idprod=$productsupplier->get_buyprice(GETPOST('idprodfournprice'), $qty);

						$idprod=$productsupplier->get_buyprice($idprodfournprice, $qty);
					// Just to see if a price exists for the quantity. Not used to found vat.
					}

					//Replaces $fk_unit with the product's
					if ($idprod > 0)
					{
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
						$price_base_type = 'HT';

						// TODO Save the product supplier ref into database into field ref_supplier (must rename field ref into ref_supplier first)
						$result=$object->addline($desc, $productsupplier->fourn_pu, $tva_tx, $localtax1_tx, $localtax2_tx, $qty, $idprod, $remise_percent, $date_start, $date_end, 0, $tva_npr, $price_base_type, $type, -1, 0, $array_options, $productsupplier->fk_unit);
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
				//else if( GETPOST('price_ht')!=='' || GETPOST('price_ttc')!=='' )
				else if( $price_ht!=='' || $price_ttc!=='' )
				{
					$pu_ht = price2num($price_ht, 'MU');
					$pu_ttc = price2num($price_ttc, 'MU');
					$tva_npr = (preg_match('/\*/', $tva_tx) ? 1 : 0);
					$tva_tx = str_replace('*', '', $tva_tx);
					$label = ($product_label ? $product_label : '');
					$desc = $product_desc;
					//$type = GETPOST('type');

					//$fk_unit= GETPOST('units', 'alpha');

					$tva_tx = price2num($tva_tx);
					// When vat is text input field

					// Local Taxes
					$localtax1_tx= get_localtax($tva_tx, 1,$mysoc,$object->thirdparty);
					$localtax2_tx= get_localtax($tva_tx, 2,$mysoc,$object->thirdparty);

					//if (!empty($_POST['price_ht']))
					if (!empty($price_ht))
					{
						//$ht = price2num($_POST['price_ht']);
						$ht = price2num($price_ht);
						$price_base_type = 'HT';
					}
					else
					{
						//$ttc = price2num($_POST['price_ttc']);
						$ttc = price2num($price_ttc);
						$ht = $ttc / (1 + ($tva_tx / 100));
						$price_base_type = 'HT';
					}

					$result=$object->addline($product_desc, $ht, $tva_tx, $localtax1_tx, $localtax2_tx, $qty, 0, $remise_percent, $date_start, $date_end, 0, $tva_npr, $price_base_type, $type, -1, 0, $array_options, $fk_unit);
				}
				$amounts[$id] += price2num($price_ttc*$qty,'MU');
				if (! $error && $result > 0)
				{
					unset($prod_entry_mode);

					unset($qty);
					unset($type);
					unset($remise_percent);
					unset($pu);
					unset($price_ht);
					unset($multicurrency_price_ht);
					unset($price_ttc);
					unset($tva_tx);
					unset($label);
					unset($localtax1_tx);
					unset($localtax2_tx);
					unset($np_marginRate);
					unset($np_markRate);
					unset($dp_desc);
					unset($idprodfournprice);
					unset($units);

					unset($date_starthour);
					unset($date_startmin);
					unset($date_startsec);
					unset($date_startday);
					unset($date_startmonth);
					unset($date_startyear);
					unset($date_endhour);
					unset($date_endmin);
					unset($date_endsec);
					unset($date_endday);
					unset($date_endmonth);
					unset($date_endyear);
				}
			}
		}
		//else $error++;


		//procedemos a validar
		$idwarehouse=GETPOST('idwarehouse');

		$object->fetch($id);
		$object->fetch_thirdparty();

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

		//echo '<hr>'.$id.' error '.$error;exit;

		if (! $error)
		{
			$result = $object->validate($user,'',$idwarehouse);
			if ($result < 0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
		}
		//para verificar recuperamos el ref
		$temp = new FactureFournisseur($db);
		$temp->fetch($id);
		//echo '<hr>REF '.$temp->ref. ' para '.$id;

		//procesamos al registro del pago
		if (!$error)
		{
			$datepaye = $datefacture;
			if (! $error)
			{
				// Creation de la ligne paiement
				$paiement = new PaiementFourn($db);
				$paiement->datepaye     = $datefacture;
				$paiement->amounts      = $amounts;
				// Array of amounts
				$paiement->multicurrency_amounts = $multicurrency_amounts;
				$paiement->paiementid   = $paiementid;
				$paiement->num_paiement = $num_paiement;
				$paiement->note         = $comment;
				//print_r($paiement);
				if (! $error)
				{
					$paiement_id = $paiement->create($user,($closepaidinvoices=='on'?1:0));
					if ($paiement_id < 0)
					{
						setEventMessages($paiement->error, $paiement->errors, 'errors');
						$error++;
					}
				}

				if (! $error)
				{
					$result=$paiement->addPaymentToBank($user,'payment_supplier','(SupplierInvoicePayment)',$fk_account,'','');
					$idBank = $result;
					if ($result < 0)
					{
						setEventMessages($paiement->error, $paiement->errors, 'errors');
						$error++;
					}
				}
			}
			else
			{
				$error++;
			}
		}
		//fin proceso pago
		//if (!$error) $db->commit();
		//else $db->rollback();
	}
	//$error++;
	return array($id,$paiement_id,$idBank,$error);
}


function payfacturefourn($id,$data)
{
	require_once DOL_DOCUMENT_ROOT.'/fourn/class/paiementfourn.class.php';
	global $db,$user,$objaccount;
	$datefacture = $data['datefacture'];
	$amounts[$id] = $data['amount'];
	$multicurrency_amounts = $data['multicurrency_amounts'];

	$objaccount->fetch($fk_account);
	if ($objaccount->courant == 2)
	{
		$data['operation']='LIQ';
	}

	$objpaiement = get_c_paiement('',$data['operation']);
	//para pago
	if ($objpaiement->id)
		$paiementid   = $objpaiement->id;
	else
	{
		if ($data['operation'] == 'LIQ')
			$paiementid = 4;
	}
	$num_paiement = $data['num_paiement'];
	$comment = $data['comment'];
	$closepaidinvoices = $data['closepaidinvoices'];
	$fk_account = $data['fk_account'];
				// Creation de la ligne paiement
	$paiement = new PaiementFourn($db);
	$paiement->datepaye     = $datefacture;
	$paiement->amounts      = $amounts;
				// Array of amounts
	$paiement->multicurrency_amounts = $multicurrency_amounts;
	$paiement->paiementid   = $paiementid;
	$paiement->num_paiement = $num_paiement;
	$paiement->note         = $comment;

	if (! $error)
	{
		$paiement_id = $paiement->create($user,($closepaidinvoices=='on'?1:0));
		if ($paiement_id < 0)
		{
			setEventMessages($paiement->error, $paiement->errors, 'errors');
			$error++;
		}
	}

	if (! $error)
	{
		$result=$paiement->addPaymentToBank($user,'payment_supplier','(SupplierInvoicePayment)',$fk_account,'','');
		$idBank = $result;
		if ($result < 0)
		{
			setEventMessages($paiement->error, $paiement->errors, 'errors');
			$error++;
		}
	}
	return array($id,$paiement_id,$idBank,$error);
}

function facturepay($data)
{
	global $langs,$conf,$user,$db;
	$fk_soc      = $data['fk_soc'];
	$ref_soc     = $data['ref_soc'];
	$type        = $data['type']+0;
	//por defecto el tipo es 0
	$datefacture = $data['datefacture'];
	$datedue     = $data['datedue'];
	$fk_projet   = $data['fk_projet'];
	$fk_account  = $data['fk_account'];
	$label       = $data['label'];
	$closepaidinvoices = $data['closepaidinvoices'];
	$mode_reglement_id = $data['mode_reglement'];
	$objpaiement = get_c_paiement('',$data['operation']);
	//para pago
	if ($objpaiement->id)
		$paiementid   = $objpaiement->id;
	else
	{
		if ($data['operation'] == 'LIQ')
			$paiementid = 4;
	}
	$num_paiement = $data['num_paiement'];
	$comment      = $data['comment'];
	$closepaidinvoice = $data['closepaidinvoice'];
	//para cerrar la factura como pagada debe ser on

	//fijos
	$cond_reglement_id = 1;
	//registro de los productos
	$aProduct = $data['aProduct'];

	//llamamos a las clases para almacenar
	require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/modules/supplier_invoice/modules_facturefournisseur.php';
	require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
	require_once DOL_DOCUMENT_ROOT.'/fourn/class/paiementfourn.class.php';
	require_once DOL_DOCUMENT_ROOT.'/core/lib/fourn.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
	require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
	if (!empty($conf->produit->enabled))
		require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
	if (!empty($conf->projet->enabled)) {
		require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
		require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
	}
	$object=new FactureFournisseur($db);
	$extrafields = new ExtraFields($db);

	// fetch optionals attributes and labels
	$extralabels=$extrafields->fetch_name_optionals_label($object->table_element);

	//procedemos al registro
	$error=0;

	if (! $error)
	{
		//$db->begin();

		$extralabels = $extrafields->fetch_name_optionals_label($object->table_element);
		$ret = $extrafields->setOptionalsFromPost($extralabels, $object);
		if ($ret < 0) $error++;

		$tmpproject = $fk_projet;

			// Creation facture
		$object->ref           = $ref;
		$object->ref_supplier  = $ref_soc;
		$object->socid         = $fk_soc;
		$object->libelle       = $label;
		$object->date          = $datefacture;
		$object->date_echeance = $datedue;
		$object->note_public   = GETPOST('note_public');
		$object->note_private  = GETPOST('note_private');
		$object->cond_reglement_id = $cond_reglement_id;
		$object->mode_reglement_id = $mode_reglement_id;
		$object->fk_account        = $fk_account;
		$object->fk_project    = ($tmpproject > 0) ? $tmpproject : null;
		//$object->fk_incoterms = GETPOST('incoterm_id', 'int');
		//$object->location_incoterms = GETPOST('location_incoterms', 'alpha');
		//$object->multicurrency_code = GETPOST('multicurrency_code', 'alpha');
		//$object->multicurrency_tx = GETPOST('originmulticurrency_tx', 'int');

			// Auto calculation of date due if not filled by user
		if(empty($object->date_echeance)) $object->date_echeance = $object->calculate_date_lim_reglement();

			// If creation from another object of another module
		if (! $error && $_POST['origin'] && $_POST['originid'])
		{
				// Parse element/subelement (ex: project_task)
			$element = $subelement = $_POST['origin'];
				/*if (preg_match('/^([^_]+)_([^_]+)/i',$_POST['origin'],$regs))
				 {
				$element = $regs[1];
				$subelement = $regs[2];
			}*/

				// For compatibility
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
			}
			if ($element == 'project')
			{
				$element = 'projet';
			}
			$object->origin    = $_POST['origin'];
			$object->origin_id = $_POST['originid'];

			$id = $object->create($user);

				// Add lines
			if ($id > 0)
			{
				require_once DOL_DOCUMENT_ROOT.'/'.$element.'/class/'.$subelement.'.class.php';
				$classname = ucfirst($subelement);
				if ($classname == 'Fournisseur.commande') $classname='CommandeFournisseur';
				$srcobject = new $classname($db);

				$result=$srcobject->fetch($_POST['originid']);
				if ($result > 0)
				{
					$lines = $srcobject->lines;
					if (empty($lines) && method_exists($srcobject,'fetch_lines'))
					{
						$srcobject->fetch_lines();
						$lines = $srcobject->lines;
					}

					$num=count($lines);
					for ($i = 0; $i < $num; $i++)
					{
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

							// FIXME Missing $lines[$i]->ref_supplier and $lines[$i]->label into addline and updateline methods. They are filled when coming from order for example.
						$result = $object->addline(
							$desc,
							$lines[$i]->subprice,
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
							'HT',
							$product_type,
							$lines[$i]->rang,
							0,
							$lines[$i]->array_options,
							$lines[$i]->fk_unit
							);

						if ($result < 0)
						{
							$error++;
							break;
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
		}
		else if (! $error)
		{
			$id = $object->create($user);
			if ($id < 0)
			{
				$error++;
			}

			if (! $error)
			{
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
		//procedemos a registrar los productos
		if ($id>0)
		{
			foreach ((array) $aProduct AS $j =>$row)
			{
				//contenido de $row
				$dp_desc = $row['dp_desc'];
				$product_desc = $row['dp_desc'];
				$prod_entry_mode = $row['prod_entry_mode'];
				$price_ht = $row['price_ht'];
				$price_ttc = $row['price_ttc'];
				$tva_tx = $row['tva_tx'];
				$idprod = $row['idprod'];
				$qty = $row['qty'];
				$remise_percent = $row['remise_percent'];
				$date_start = $row['date_start'];
				$date_end = $row['date_end'];
				$idprodfournprice = $row['idprodfournprice'];
				$type = $row['type'];
				$fk_unit = $row['fk_unit'];
				// 0 = producto
				// 1 = servicio
				//$db->begin();

				$ret=$object->fetch($id);
				if ($ret < 0)
				{
					dol_print_error($db,$object->error);
					exit;
				}
				$ret=$object->fetch_thirdparty();

				$langs->load('errors');

				// Set if we used free entry or predefined product
				$predef='';

				//$product_desc=(GETPOST('dp_desc')?GETPOST('dp_desc'):'');
				//if (GETPOST('prod_entry_mode') == 'free')

				if ($prod_entry_mode == 'free')
				{
					$idprod=0;

					//$price_ht = GETPOST('price_ht');
					//$tva_tx = (GETPOST('tva_tx') ? GETPOST('tva_tx') : 0);
				}
				else
				{
					//$idprod=GETPOST('idprod', 'int');
					$price_ht = '';
					$tva_tx = '';
				}

				//$qty = GETPOST('qty'.$predef);
				//$remise_percent=GETPOST('remise_percent'.$predef);

				//$date_start=dol_mktime(GETPOST('date_start'.$predef.'hour'), GETPOST('date_start'.$predef.'min'), GETPOST('date_start' . $predef . 'sec'), GETPOST('date_start'.$predef.'month'), GETPOST('date_start'.$predef.'day'), GETPOST('date_start'.$predef.'year'));
				//$date_end=dol_mktime(GETPOST('date_end'.$predef.'hour'), GETPOST('date_end'.$predef.'min'), GETPOST('date_end' . $predef . 'sec'), GETPOST('date_end'.$predef.'month'), GETPOST('date_end'.$predef.'day'), GETPOST('date_end'.$predef.'year'));

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
				//if (GETPOST('prod_entry_mode')=='free' && GETPOST('price_ht') < 0 && $qty < 0)
				if ($prod_entry_mode=='free' && $price_ht < 0 && $qty < 0)
				{
					setEventMessages($langs->trans('ErrorBothFieldCantBeNegative', $langs->transnoentitiesnoconv('UnitPrice'), $langs->transnoentitiesnoconv('Qty')), null, 'errors');
					$error++;
				}
				//if (GETPOST('prod_entry_mode')=='free'  && ! GETPOST('idprodfournprice') && GETPOST('type') < 0)
				if ($prod_entry_mode=='free'  && ! $idprodfournprice && $type < 0)
				{
					setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Type')), null, 'errors');
					$error++;
				}
				//if (GETPOST('prod_entry_mode')=='free' && GETPOST('price_ht')==='' && GETPOST('price_ttc')==='')
				// Unit price can be 0 but not ''
				if ($prod_entry_mode=='free' && $price_ht==='' && $price_ttc==='')

				{
					setEventMessages($langs->trans($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('UnitPrice'))), null, 'errors');
					$error++;
				}
				//if (GETPOST('prod_entry_mode')=='free' && ! GETPOST('dp_desc'))
				if ($prod_entry_mode=='free' && ! $dp_desc)

				{
					setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Description')), null, 'errors');
					$error++;
				}
				//if (! GETPOST('qty'))
				if (! $qty)
				{
					setEventMessages($langs->trans('ErrorFieldRequired', $langs->transnoentitiesnoconv('Qty')), null, 'errors');
					$error++;
				}
				//if (GETPOST('prod_entry_mode') != 'free')
				if ($prod_entry_mode != 'free')
				// With combolist mode idprodfournprice is > 0 or -1. With autocomplete, idprodfournprice is > 0 or ''
				{
					$idprod=0;
					$productsupplier=new ProductFournisseur($db);

					//if (GETPOST('idprodfournprice') == -1 || GETPOST('idprodfournprice') == '') $idprod=-2;
					if ($idprodfournprice == -1 || $idprodfournprice == '') $idprod=-2;
					// Same behaviour than with combolist. When not select idprodfournprice is now -2 (to avoid conflict with next action that may return -1)

					//if (GETPOST('idprodfournprice') > 0)
					if ($idprodfournprice > 0)
					{
						//$idprod=$productsupplier->get_buyprice(GETPOST('idprodfournprice'), $qty);

						$idprod=$productsupplier->get_buyprice($idprodfournprice, $qty);
					// Just to see if a price exists for the quantity. Not used to found vat.
					}

					//Replaces $fk_unit with the product's
					if ($idprod > 0)
					{
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
						$price_base_type = 'HT';

						// TODO Save the product supplier ref into database into field ref_supplier (must rename field ref into ref_supplier first)
						$result=$object->addline($desc, $productsupplier->fourn_pu, $tva_tx, $localtax1_tx, $localtax2_tx, $qty, $idprod, $remise_percent, $date_start, $date_end, 0, $tva_npr, $price_base_type, $type, -1, 0, $array_options, $productsupplier->fk_unit);
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
				//else if( GETPOST('price_ht')!=='' || GETPOST('price_ttc')!=='' )
				else if( $price_ht!=='' || $price_ttc!=='' )
				{
					$pu_ht = price2num($price_ht, 'MU');
					$pu_ttc = price2num($price_ttc, 'MU');
					$tva_npr = (preg_match('/\*/', $tva_tx) ? 1 : 0);
					$tva_tx = str_replace('*', '', $tva_tx);
					$label = ($product_label ? $product_label : '');
					$desc = $product_desc;
					//$type = GETPOST('type');

					//$fk_unit= GETPOST('units', 'alpha');

					$tva_tx = price2num($tva_tx);
					// When vat is text input field

					// Local Taxes
					$localtax1_tx= get_localtax($tva_tx, 1,$mysoc,$object->thirdparty);
					$localtax2_tx= get_localtax($tva_tx, 2,$mysoc,$object->thirdparty);

					//if (!empty($_POST['price_ht']))
					if (!empty($price_ht))
					{
						//$ht = price2num($_POST['price_ht']);
						$ht = price2num($price_ht);
						$price_base_type = 'HT';
					}
					else
					{
						//$ttc = price2num($_POST['price_ttc']);
						$ttc = price2num($price_ttc);
						$ht = $ttc / (1 + ($tva_tx / 100));
						$price_base_type = 'HT';
					}

					$result=$object->addline($product_desc, $ht, $tva_tx, $localtax1_tx, $localtax2_tx, $qty, 0, $remise_percent, $date_start, $date_end, 0, $tva_npr, $price_base_type, $type, -1, 0, $array_options, $fk_unit);
				}
				$amounts[$id] += price2num($price_ttc*$qty,'MU');
				if (! $error && $result > 0)
				{
					unset($prod_entry_mode);

					unset($qty);
					unset($type);
					unset($remise_percent);
					unset($pu);
					unset($price_ht);
					unset($multicurrency_price_ht);
					unset($price_ttc);
					unset($tva_tx);
					unset($label);
					unset($localtax1_tx);
					unset($localtax2_tx);
					unset($np_marginRate);
					unset($np_markRate);
					unset($dp_desc);
					unset($idprodfournprice);
					unset($units);

					unset($date_starthour);
					unset($date_startmin);
					unset($date_startsec);
					unset($date_startday);
					unset($date_startmonth);
					unset($date_startyear);
					unset($date_endhour);
					unset($date_endmin);
					unset($date_endsec);
					unset($date_endday);
					unset($date_endmonth);
					unset($date_endyear);
				}
			}
		}
		else $error++;
		//procedemos a validar
		$idwarehouse=GETPOST('idwarehouse');

		$object->fetch($id);
		$object->fetch_thirdparty();

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
			$result = $object->validate($user,'',$idwarehouse);
			if ($result < 0)
			{
				$error++;
				setEventMessages($object->error,$object->errors,'errors');
			}
		}
		//para verificar recuperamos el ref
		$temp = new FactureFournisseur($db);
		$temp->fetch($id);
		//echo '<hr>REF '.$temp->ref. ' para '.$id;
		//procesamos al registro del pago
		if (!$error)
		{
			$datepaye = $datefacture;
			if (! $error)
			{

				// Creation de la ligne paiement
				$paiement = new PaiementFourn($db);
				$paiement->datepaye     = $datefacture;
				$paiement->amounts      = $amounts;
				// Array of amounts
				$paiement->multicurrency_amounts = $multicurrency_amounts;
				$paiement->paiementid   = $paiementid;
				$paiement->num_paiement = $num_paiement;
				$paiement->note         = $comment;
				if (! $error)
				{
					$paiement_id = $paiement->create($user,($closepaidinvoices=='on'?1:0));
					if ($paiement_id < 0)
					{
						setEventMessages($paiement->error, $paiement->errors, 'errors');
						$error++;
					}
				}

				if (! $error)
				{
					$result=$paiement->addPaymentToBank($user,'payment_supplier','(SupplierInvoicePayment)',$fk_account,'','');
					$idBank = $result;
					if ($result < 0)
					{
						setEventMessages($paiement->error, $paiement->errors, 'errors');
						$error++;
					}
				}
			}
			else
			{
				$error++;
			}
		}
		//fin proceso pago
		//if (!$error) $db->commit();
		//else $db->rollback();
	}
	return array($id,$paiement_id,$idBank,$error);
}
?>