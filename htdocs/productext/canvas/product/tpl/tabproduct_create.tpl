<?php


	// -----------------------------------------
	// When used in standard mode
	// -----------------------------------------
	if ($action == 'create' && ($user->rights->produit->creer || $user->rights->service->creer))
	{
		//WYSIWYG Editor
		require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

		// Load object modCodeProduct
		$module=(! empty($conf->global->PRODUCT_CODEPRODUCT_ADDON)?$conf->global->PRODUCT_CODEPRODUCT_ADDON:'mod_codeproduct_leopard');
		if (substr($module, 0, 16) == 'mod_codeproduct_' && substr($module, -3) == 'php')
		{
			$module = substr($module, 0, dol_strlen($module)-4);
		}
		$result=dol_include_once('/core/modules/product/'.$module.'.php');
		if ($result > 0)
		{
			$modCodeProduct = new $module();
		}

		print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="add">';
		print '<input type="hidden" name="type" value="'.$type.'">'."\n";
		if (! empty($modCodeProduct->code_auto))
			print '<input type="hidden" name="code_auto" value="1">';
		if (! empty($modBarCodeProduct->code_auto))
			print '<input type="hidden" name="barcode_auto" value="1">';

		if ($type==1) $title=$langs->trans("NewService");
		else $title=$langs->trans("NewProduct");
		$linkback="";
		print load_fiche_titre($title,$linkback,'title_products.png');

		dol_fiche_head('');

		print '<table class="border centpercent">';

		print '<tr>';
		$tmpcode='';
		if (! empty($modCodeProduct->code_auto)) $tmpcode=$modCodeProduct->getNextValue($object,$type);
		print '<td class="titlefieldcreate fieldrequired">'.$langs->trans("Ref").'</td><td colspan="3"><input name="ref" class="maxwidth200" maxlength="128" value="'.dol_escape_htmltag(GETPOST('ref')?GETPOST('ref'):$tmpcode).'">';
		if ($refalreadyexists)
		{
			print $langs->trans("RefAlreadyExists");
		}
		print '</td></tr>';

		// Label
		print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td colspan="3"><input name="label" class="minwidth300 maxwidth400onsmartphone" maxlength="255" value="'.dol_escape_htmltag(GETPOST('label')).'"></td></tr>';

		// On sell
		print '<tr><td class="fieldrequired">'.$langs->trans("Status").' ('.$langs->trans("Sell").')</td><td colspan="3">';
		$statutarray=array('1' => $langs->trans("OnSell"), '0' => $langs->trans("NotOnSell"));
		print $form->selectarray('statut',$statutarray,GETPOST('statut'));
		print '</td></tr>';

		// To buy
		print '<tr><td class="fieldrequired">'.$langs->trans("Status").' ('.$langs->trans("Buy").')</td><td colspan="3">';
		$statutarray=array('1' => $langs->trans("ProductStatusOnBuy"), '0' => $langs->trans("ProductStatusNotOnBuy"));
		print $form->selectarray('statut_buy',$statutarray,GETPOST('statut_buy'));
		print '</td></tr>';

		// Batch number management
		if (! empty($conf->productbatch->enabled))
		{
			print '<tr><td>'.$langs->trans("ManageLotSerial").'</td><td colspan="3">';
			$statutarray=array('0' => $langs->trans("ProductStatusNotOnBatch"), '1' => $langs->trans("ProductStatusOnBatch"));
			print $form->selectarray('status_batch',$statutarray,GETPOST('status_batch'));
			print '</td></tr>';
		}

		$showbarcode=empty($conf->barcode->enabled)?0:1;
		if (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->barcode->lire_advance)) $showbarcode=0;

		if ($showbarcode)
		{
			print '<tr><td>'.$langs->trans('BarcodeType').'</td><td>';
			if (isset($_POST['fk_barcode_type']))
			{
				$fk_barcode_type=GETPOST('fk_barcode_type');
			}
			else
			{
				if (empty($fk_barcode_type) && ! empty($conf->global->PRODUIT_DEFAULT_BARCODE_TYPE)) $fk_barcode_type = $conf->global->PRODUIT_DEFAULT_BARCODE_TYPE;
			}
			require_once DOL_DOCUMENT_ROOT.'/core/class/html.formbarcode.class.php';
			$formbarcode = new FormBarCode($db);
			print $formbarcode->select_barcode_type($fk_barcode_type, 'fk_barcode_type', 1);
			print '</td><td>'.$langs->trans("BarcodeValue").'</td><td>';
			$tmpcode=isset($_POST['barcode'])?GETPOST('barcode'):$object->barcode;
			if (empty($tmpcode) && ! empty($modBarCodeProduct->code_auto)) $tmpcode=$modBarCodeProduct->getNextValue($object,$type);
			print '<input class="maxwidth100" type="text" name="barcode" value="'.dol_escape_htmltag($tmpcode).'">';
			print '</td></tr>';
		}

		// Description (used in invoice, propal...)
		print '<tr><td class="tdtop">'.$langs->trans("Description").'</td><td colspan="3">';

		$doleditor = new DolEditor('desc', GETPOST('desc'), '', 160, 'dolibarr_details', '', false, true, $conf->global->FCKEDITOR_ENABLE_PRODUCTDESC, ROWS_4, '90%');
		$doleditor->Create();

		print "</td></tr>";

		// Public URL
		print '<tr><td>'.$langs->trans("PublicUrl").'</td><td colspan="3">';
		print '<input type="text" name="url" class="quatrevingtpercent" value="'.GETPOST('url').'">';
		print '</td></tr>';

		// Stock min level
		if ($type != 1 && ! empty($conf->stock->enabled))
		{
			print '<tr><td>'.$langs->trans("StockLimit").'</td><td>';
			print '<input name="seuil_stock_alerte" class="maxwidth50" value="'.GETPOST('seuil_stock_alerte').'">';
			print '</td>';
			// Stock desired level
			print '<td>'.$langs->trans("DesiredStock").'</td><td>';
			print '<input name="desiredstock" class="maxwidth50" value="'.GETPOST('desiredstock').'">';
			print '</td></tr>';
		}
		else
		{
			print '<input name="seuil_stock_alerte" type="hidden" value="0">';
			print '<input name="desiredstock" type="hidden" value="0">';
		}

		// Nature
		if ($type != 1)
		{
			print '<tr><td>'.$langs->trans("Nature").'</td><td colspan="3">';
			$statutarray=array('1' => $langs->trans("Finished"), '0' => $langs->trans("RowMaterial"));
			print $form->selectarray('finished',$statutarray,GETPOST('finished'),1);
			print '</td></tr>';
		}

		// Duration
		if ($type == 1)
		{
			print '<tr><td>' . $langs->trans("Duration") . '</td><td colspan="3"><input name="duration_value" size="6" maxlength="5" value="' . $duration_value . '"> &nbsp;';
			print '<input name="duration_unit" type="radio" value="h">'.$langs->trans("Hour").'&nbsp;';
			print '<input name="duration_unit" type="radio" value="d">'.$langs->trans("Day").'&nbsp;';
			print '<input name="duration_unit" type="radio" value="w">'.$langs->trans("Week").'&nbsp;';
			print '<input name="duration_unit" type="radio" value="m">'.$langs->trans("Month").'&nbsp;';
			print '<input name="duration_unit" type="radio" value="y">'.$langs->trans("Year").'&nbsp;';
			print '</td></tr>';
		}

		if ($type != 1)	// Le poids et le volume ne concerne que les produits et pas les services
		{
			// Weight
			print '<tr><td>'.$langs->trans("Weight").'</td><td colspan="3">';
			print '<input name="weight" size="4" value="'.GETPOST('weight').'">';
			print $formproduct->select_measuring_units("weight_units","weight");
			print '</td></tr>';
			// Length
			if (empty($conf->global->PRODUCT_DISABLE_LENGTH))
			{
				print '<tr><td>'.$langs->trans("Length").'</td><td colspan="3">';
				print '<input name="size" size="4" value="'.GETPOST('size').'">';
				print $formproduct->select_measuring_units("size_units","size");
				print '</td></tr>';
			}
			if (empty($conf->global->PRODUCT_DISABLE_SURFACE))
			{
				// Surface
				print '<tr><td>'.$langs->trans("Surface").'</td><td colspan="3">';
				print '<input name="surface" size="4" value="'.GETPOST('surface').'">';
				print $formproduct->select_measuring_units("surface_units","surface");
				print '</td></tr>';
			}
			// Volume
			print '<tr><td>'.$langs->trans("Volume").'</td><td colspan="3">';
			print '<input name="volume" size="4" value="'.GETPOST('volume').'">';
			print $formproduct->select_measuring_units("volume_units","volume");
			print '</td></tr>';
		}

		// Units
		if($conf->global->PRODUCT_USE_UNITS)
		{
			print '<tr><td>'.$langs->trans('DefaultUnitToShow').'</td>';
			print '<td colspan="3">';
			print $form->selectUnits('','units');
			print '</td></tr>';
		}

		// Custom code
		if (empty($conf->global->PRODUCT_DISABLE_CUSTOM_INFO) && empty($type))
		{
			print '<tr><td>'.$langs->trans("CustomCode").'</td><td><input name="customcode" class="maxwidth100onsmartphone" value="'.GETPOST('customcode').'"></td>';
			// Origin country
			print '<td>'.$langs->trans("CountryOrigin").'</td><td>';
			print $form->select_country(GETPOST('country_id','int'),'country_id');
			if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
			print '</td></tr>';
		}

		// Other attributes
		$parameters=array('colspan' => 3);
		$reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
		if (empty($reshook) && ! empty($extrafields->attribute_label))
		{
			print $object->showOptionals($extrafields,'edit',$parameters);
		}

		// Note (private, no output on invoices, propales...)
		//if (! empty($conf->global->MAIN_DISABLE_NOTES_TAB))       available in create mode
		//{
		print '<tr><td class="tdtop">'.$langs->trans("NoteNotVisibleOnBill").'</td><td colspan="3">';

			// We use dolibarr_details as type of DolEditor here, because we must not accept images as description is included into PDF and not accepted by TCPDF.
		$doleditor = new DolEditor('note_private', GETPOST('note_private'), '', 140, 'dolibarr_details', '', false, true, $conf->global->FCKEDITOR_ENABLE_PRODUCTDESC, ROWS_8, '90%');
		$doleditor->Create();

		print "</td></tr>";
		//}

		if($conf->categorie->enabled) {
			// Categories
			print '<tr><td>'.$langs->trans("Categories").'</td><td colspan="3">';
			$cate_arbo = $form->select_all_categories(Categorie::TYPE_PRODUCT, '', 'parent', 64, 0, 1);
			print $form->multiselectarray('categories', $cate_arbo, $arrayselected, '', 0, '', 0, '100%');
			print "</td></tr>";
		}

		print '</table>';

		print '<br>';

		if (! empty($conf->global->PRODUIT_MULTIPRICES))
		{
			// We do no show price array on create when multiprices enabled.
			// We must set them on prices tab.
		}
		else
		{
			print '<table class="border" width="100%">';

			// Price
			print '<tr><td class="titlefieldcreate">'.$langs->trans("SellingPrice").'</td>';
			print '<td><input name="price" class="maxwidth50" value="'.$object->price.'">';
			print $form->selectPriceBaseType($object->price_base_type, "price_base_type");
			print '</td></tr>';

			// Min price
			print '<tr><td>'.$langs->trans("MinPrice").'</td>';
			print '<td><input name="price_min" class="maxwidth50" value="'.$object->price_min.'">';
			print '</td></tr>';

			// VAT
			print '<tr><td>'.$langs->trans("VATRate").'</td><td>';
			print $form->load_tva("tva_tx",-1,$mysoc,'');
			print '</td></tr>';

			print '</table>';

			print '<br>';
		}

		// Accountancy codes
		print '<table class="border" width="100%">';

		if (! empty($conf->accounting->enabled))
		{
			// Accountancy_code_sell
			print '<tr><td class="titlefieldcreate">'.$langs->trans("ProductAccountancySellCode").'</td>';
			print '<td>';
			print $formaccountancy->select_account(GETPOST('accountancy_code_sell'), 'accountancy_code_sell', 1, null, 1, 1, '');
			print '</td></tr>';

			// Accountancy_code_buy
			print '<tr><td>'.$langs->trans("ProductAccountancyBuyCode").'</td>';
			print '<td>';
			print $formaccountancy->select_account(GETPOST('accountancy_code_buy'), 'accountancy_code_buy', 1, null, 1, 1, '');
			print '</td></tr>';
		}
		else // For external software
		{
			// Accountancy_code_sell
			print '<tr><td class="titlefieldcreate">'.$langs->trans("ProductAccountancySellCode").'</td>';
			print '<td class="maxwidthonsmartphone"><input class="minwidth100" name="accountancy_code_sell" value="'.$object->accountancy_code_sell.'">';
			print '</td></tr>';

			// Accountancy_code_buy
			print '<tr><td>'.$langs->trans("ProductAccountancyBuyCode").'</td>';
			print '<td class="maxwidthonsmartphone"><input class="minwidth100" name="accountancy_code_buy" value="'.$object->accountancy_code_buy.'">';
			print '</td></tr>';
		}
		print '</table>';

		dol_fiche_end();

		print '<div class="center">';
		print '<input type="submit" class="button" value="' . $langs->trans("Create") . '">';
		print ' &nbsp; &nbsp; ';
		print '<input type="button" class="button" value="' . $langs->trans("Cancel") . '" onClick="javascript:history.go(-1)">';
		print '</div>';

		print '</form>';
	}

	/*
	 * Product card
	 */

	else if ($object->id > 0)
	{
		// Fiche en mode edition
		if ($action == 'edit' &&  ((($object->type == Product::TYPE_PRODUCT && $user->rights->produit->creer) ||  ($object->type == Product::TYPE_SERVICE && $user->rights->service->creer))))
		{
			//WYSIWYG Editor
			require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

			$type = $langs->trans('Product');
			if ($object->isService()) $type = $langs->trans('Service');
			//print load_fiche_titre($langs->trans('Modify').' '.$type.' : '.(is_object($object->oldcopy)?$object->oldcopy->ref:$object->ref), "");

			// Main official, simple, and not duplicated code
			print '<form action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'" method="POST">'."\n";
			print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
			print '<input type="hidden" name="action" value="update">';
			print '<input type="hidden" name="id" value="'.$object->id.'">';
			print '<input type="hidden" name="canvas" value="'.$object->canvas.'">';

			$head=product_prepare_head($object);
			$titre=$langs->trans("CardProduct".$object->type);
			$picto=($object->type== Product::TYPE_SERVICE?'service':'product');
			dol_fiche_head($head, 'card', $titre, 0, $picto);

			print '<table class="border allwidth">';

			// Ref
			print '<tr><td class="titlefield fieldrequired">'.$langs->trans("Ref").'</td><td colspan="3"><input name="ref" class="maxwidth200" maxlength="128" value="'.dol_escape_htmltag($object->ref).'"></td></tr>';

			// Label
			print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td colspan="3"><input name="label" class="minwidth300 maxwidth400onsmartphone" maxlength="255" value="'.dol_escape_htmltag($object->label).'"></td></tr>';

			// Status To sell
			print '<tr><td class="fieldrequired">'.$langs->trans("Status").' ('.$langs->trans("Sell").')</td><td colspan="3">';
			print '<select class="flat" name="statut">';
			if ($object->status)
			{
				print '<option value="1" selected>'.$langs->trans("OnSell").'</option>';
				print '<option value="0">'.$langs->trans("NotOnSell").'</option>';
			}
			else
			{
				print '<option value="1">'.$langs->trans("OnSell").'</option>';
				print '<option value="0" selected>'.$langs->trans("NotOnSell").'</option>';
			}
			print '</select>';
			print '</td></tr>';

			// Status To Buy
			print '<tr><td class="fieldrequired">'.$langs->trans("Status").' ('.$langs->trans("Buy").')</td><td colspan="3">';
			print '<select class="flat" name="statut_buy">';
			if ($object->status_buy)
			{
				print '<option value="1" selected>'.$langs->trans("ProductStatusOnBuy").'</option>';
				print '<option value="0">'.$langs->trans("ProductStatusNotOnBuy").'</option>';
			}
			else
			{
				print '<option value="1">'.$langs->trans("ProductStatusOnBuy").'</option>';
				print '<option value="0" selected>'.$langs->trans("ProductStatusNotOnBuy").'</option>';
			}
			print '</select>';
			print '</td></tr>';

			// Batch number managment
			if ($conf->productbatch->enabled) {
				print '<tr><td>'.$langs->trans("ManageLotSerial").'</td><td colspan="3">';
				$statutarray=array('0' => $langs->trans("ProductStatusNotOnBatch"), '1' => $langs->trans("ProductStatusOnBatch"));
				print $form->selectarray('status_batch',$statutarray,$object->status_batch);
				print '</td></tr>';
			}

			// Barcode
			$showbarcode=empty($conf->barcode->enabled)?0:1;
			if (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->barcode->lire_advance)) $showbarcode=0;

			if ($showbarcode)
			{
				print '<tr><td>'.$langs->trans('BarcodeType').'</td><td>';
				if (isset($_POST['fk_barcode_type']))
				{
					$fk_barcode_type=GETPOST('fk_barcode_type');
				}
				else
				{
					$fk_barcode_type=$object->barcode_type;
					if (empty($fk_barcode_type) && ! empty($conf->global->PRODUIT_DEFAULT_BARCODE_TYPE)) $fk_barcode_type = $conf->global->PRODUIT_DEFAULT_BARCODE_TYPE;
				}
				require_once DOL_DOCUMENT_ROOT.'/core/class/html.formbarcode.class.php';
				$formbarcode = new FormBarCode($db);
				print $formbarcode->select_barcode_type($fk_barcode_type, 'fk_barcode_type', 1);
				print '</td><td>'.$langs->trans("BarcodeValue").'</td><td>';
				$tmpcode=isset($_POST['barcode'])?GETPOST('barcode'):$object->barcode;
				if (empty($tmpcode) && ! empty($modBarCodeProduct->code_auto)) $tmpcode=$modBarCodeProduct->getNextValue($object,$type);
				print '<input size="40" class="maxwidthonsmartphone" type="text" name="barcode" value="'.dol_escape_htmltag($tmpcode).'">';
				print '</td></tr>';
			}

			// Description (used in invoice, propal...)
			print '<tr><td class="tdtop">'.$langs->trans("Description").'</td><td colspan="3">';

			// We use dolibarr_details as type of DolEditor here, because we must not accept images as description is included into PDF and not accepted by TCPDF.
			$doleditor = new DolEditor('desc', $object->description, '', 160, 'dolibarr_details', '', false, true, $conf->global->FCKEDITOR_ENABLE_PRODUCTDESC, ROWS_4, '90%');
			$doleditor->Create();

			print "</td></tr>";
			print "\n";

			// Public Url
			print '<tr><td>'.$langs->trans("PublicUrl").'</td><td colspan="3">';
			print '<input type="text" name="url" class="quatrevingtpercent" value="'.$object->url.'">';
			print '</td></tr>';

			// Stock
			/*
			if ($object->isProduct() && ! empty($conf->stock->enabled))
			{
				print "<tr>".'<td>'.$langs->trans("StockLimit").'</td><td>';
				print '<input name="seuil_stock_alerte" size="4" value="'.$object->seuil_stock_alerte.'">';
				print '</td>';

				print '<td>'.$langs->trans("DesiredStock").'</td><td>';
				print '<input name="desiredstock" size="4" value="'.$object->desiredstock.'">';
				print '</td></tr>';
			}
			else
			{
				print '<input name="seuil_stock_alerte" type="hidden" value="'.$object->seuil_stock_alerte.'">';
				print '<input name="desiredstock" type="hidden" value="'.$object->desiredstock.'">';
			}*/

			// Nature
			if($object->type!= Product::TYPE_SERVICE)
			{
				print '<tr><td>'.$langs->trans("Nature").'</td><td colspan="3">';
				$statutarray=array('-1'=>'&nbsp;', '1' => $langs->trans("Finished"), '0' => $langs->trans("RowMaterial"));
				print $form->selectarray('finished',$statutarray,$object->finished);
				print '</td></tr>';
			}

			if ($object->isService())
			{
				// Duration
				print '<tr><td>'.$langs->trans("Duration").'</td><td colspan="3"><input name="duration_value" size="3" maxlength="5" value="'.$object->duration_value.'">';
				print '&nbsp; ';
				print '<input name="duration_unit" type="radio" value="h"'.($object->duration_unit=='h'?' checked':'').'>'.$langs->trans("Hour");
				print '&nbsp; ';
				print '<input name="duration_unit" type="radio" value="d"'.($object->duration_unit=='d'?' checked':'').'>'.$langs->trans("Day");
				print '&nbsp; ';
				print '<input name="duration_unit" type="radio" value="w"'.($object->duration_unit=='w'?' checked':'').'>'.$langs->trans("Week");
				print '&nbsp; ';
				print '<input name="duration_unit" type="radio" value="m"'.($object->duration_unit=='m'?' checked':'').'>'.$langs->trans("Month");
				print '&nbsp; ';
				print '<input name="duration_unit" type="radio" value="y"'.($object->duration_unit=='y'?' checked':'').'>'.$langs->trans("Year");

				print '</td></tr>';
			}
			else
			{
				// Weight
				print '<tr><td>'.$langs->trans("Weight").'</td><td colspan="3">';
				print '<input name="weight" size="5" value="'.$object->weight.'"> ';
				print $formproduct->select_measuring_units("weight_units", "weight", $object->weight_units);
				print '</td></tr>';
				if (empty($conf->global->PRODUCT_DISABLE_LENGTH))
				{
					// Length
					print '<tr><td>'.$langs->trans("Length").'</td><td colspan="3">';
					print '<input name="size" size="5" value="'.$object->length.'"> ';
					print $formproduct->select_measuring_units("size_units", "size", $object->length_units);
					print '</td></tr>';
				}
				if (empty($conf->global->PRODUCT_DISABLE_SURFACE))
				{
					// Surface
					print '<tr><td>'.$langs->trans("Surface").'</td><td colspan="3">';
					print '<input name="surface" size="5" value="'.$object->surface.'"> ';
					print $formproduct->select_measuring_units("surface_units", "surface", $object->surface_units);
					print '</td></tr>';
				}
				// Volume
				print '<tr><td>'.$langs->trans("Volume").'</td><td colspan="3">';
				print '<input name="volume" size="5" value="'.$object->volume.'"> ';
				print $formproduct->select_measuring_units("volume_units", "volume", $object->volume_units);
				print '</td></tr>';
			}
			// Units
			if($conf->global->PRODUCT_USE_UNITS)
			{
				print '<tr><td>'.$langs->trans('DefaultUnitToShow').'</td>';
				print '<td colspan="3">';
				print $form->selectUnits($object->fk_unit, 'units');
				print '</td></tr>';
			}

			// Custom code
			if (! $object->isService() && empty($conf->global->PRODUCT_DISABLE_CUSTOM_INFO))
			{
				print '<tr><td>'.$langs->trans("CustomCode").'</td><td><input name="customcode" class="maxwidth100onsmartphone" value="'.$object->customcode.'"></td>';
				// Origin country
				print '<td>'.$langs->trans("CountryOrigin").'</td><td>';
				print $form->select_country($object->country_id, 'country_id', '', 0, 'minwidth100 maxwidthonsmartphone');
				if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
				print '</td></tr>';
			}

			// Other attributes
			$parameters=array('colspan' => ' colspan="2"');
			$reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
			if (empty($reshook) && ! empty($extrafields->attribute_label))
			{
				print $object->showOptionals($extrafields,'edit');
			}

			// Tags-Categories
			if ($conf->categorie->enabled)
			{
				print '<tr><td class="tdtop">'.$langs->trans("Categories").'</td><td colspan="3">';
				$cate_arbo = $form->select_all_categories(Categorie::TYPE_PRODUCT, '', 'parent', 64, 0, 1);
				$c = new Categorie($db);
				$cats = $c->containing($object->id,Categorie::TYPE_PRODUCT);
				foreach($cats as $cat) {
					$arrayselected[] = $cat->id;
				}
				print $form->multiselectarray('categories', $cate_arbo, $arrayselected, '', 0, '', 0, '100%');
				print "</td></tr>";
			}

			// Note private
			if (! empty($conf->global->MAIN_DISABLE_NOTES_TAB))
			{
				print '<tr><td class="tdtop">'.$langs->trans("NoteNotVisibleOnBill").'</td><td colspan="3">';

				$doleditor = new DolEditor('note_private', $object->note_private, '', 140, 'dolibarr_notes', '', false, true, $conf->global->FCKEDITOR_ENABLE_PRODUCTDESC, ROWS_4, '90%');
				$doleditor->Create();

				print "</td></tr>";
			}

			print '</table>';

			print '<br>';

			print '<table class="border" width="100%">';

			if (! empty($conf->accounting->enabled))
			{
				// Accountancy_code_sell
				print '<tr><td class="titlefield">'.$langs->trans("ProductAccountancySellCode").'</td>';
				print '<td>';
				print $formaccountancy->select_account($object->accountancy_code_sell, 'accountancy_code_sell', 1, '', 1, 1);
				print '</td></tr>';

				// Accountancy_code_buy
				print '<tr><td>'.$langs->trans("ProductAccountancyBuyCode").'</td>';
				print '<td>';
				print $formaccountancy->select_account($object->accountancy_code_buy, 'accountancy_code_buy', 1, '', 1, 1);
				print '</td></tr>';
			}
			else // For external software
			{
				// Accountancy_code_sell
				print '<tr><td class="titlefield">'.$langs->trans("ProductAccountancySellCode").'</td>';
				print '<td><input name="accountancy_code_sell" class="maxwidth200" value="'.$object->accountancy_code_sell.'">';
				print '</td></tr>';

				// Accountancy_code_buy
				print '<tr><td>'.$langs->trans("ProductAccountancyBuyCode").'</td>';
				print '<td><input name="accountancy_code_buy" class="maxwidth200" value="'.$object->accountancy_code_buy.'">';
				print '</td></tr>';
			}
			print '</table>';

			dol_fiche_end();

			print '<div class="center">';
			print '<input type="submit" class="button" value="'.$langs->trans("Save").'">';
			print '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
			print '</div>';

			print '</form>';
		}
		// Fiche en mode visu
		else
		{
			$showbarcode=empty($conf->barcode->enabled)?0:1;
			if (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->barcode->lire_advance)) $showbarcode=0;

			$head=product_prepare_head($object);
			$titre=$langs->trans("CardProduct".$object->type);
			$picto=($object->type== Product::TYPE_SERVICE?'service':'product');
			dol_fiche_head($head, 'card', $titre, 0, $picto);

			$linkback = '<a href="'.DOL_URL_ROOT.'/product/list.php?type='.$object->type.'">'.$langs->trans("BackToList").'</a>';
			$object->next_prev_filter=" fk_product_type = ".$object->type;

			dol_banner_tab($object, 'ref', $linkback, ($user->societe_id?0:1), 'ref');


			print '<div class="fichecenter">';
			print '<div class="fichehalfleft">';

			print '<div class="underbanner clearboth"></div>';
			print '<table class="border tableforfield" width="100%">';

			// Type
			if (! empty($conf->produit->enabled) && ! empty($conf->service->enabled))
			{
				// TODO change for compatibility with edit in place
				$typeformat='select;0:'.$langs->trans("Product").',1:'.$langs->trans("Service");
				print '<tr><td class="titlefield">'.$form->editfieldkey("Type",'fk_product_type',$object->type,$object,$user->rights->produit->creer||$user->rights->service->creer,$typeformat).'</td><td colspan="2">';
				print $form->editfieldval("Type",'fk_product_type',$object->type,$object,$user->rights->produit->creer||$user->rights->service->creer,$typeformat);
				print '</td></tr>';
			}

			if ($showbarcode)
			{
				// Barcode type
				print '<tr><td class="nowrap">';
				print '<table width="100%" class="nobordernopadding"><tr><td class="nowrap">';
				print $langs->trans("BarcodeType");
				print '</td>';
				if (($action != 'editbarcodetype') && ! empty($user->rights->produit->creer) && $createbarcode) print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editbarcodetype&amp;id='.$object->id.'">'.img_edit($langs->trans('Edit'),1).'</a></td>';
				print '</tr></table>';
				print '</td><td colspan="2">';
				if ($action == 'editbarcodetype' || $action == 'editbarcode')
				{
					require_once DOL_DOCUMENT_ROOT.'/core/class/html.formbarcode.class.php';
					$formbarcode = new FormBarCode($db);
				}
				if ($action == 'editbarcodetype')
				{
					$formbarcode->form_barcode_type($_SERVER['PHP_SELF'].'?id='.$object->id,$object->barcode_type,'fk_barcode_type');
				}
				else
				{
					$object->fetch_barcode();
					print $object->barcode_type_label?$object->barcode_type_label:($object->barcode?'<div class="warning">'.$langs->trans("SetDefaultBarcodeType").'<div>':'');
				}
				print '</td></tr>'."\n";

				// Barcode value
				print '<tr><td class="nowrap">';
				print '<table width="100%" class="nobordernopadding"><tr><td class="nowrap">';
				print $langs->trans("BarcodeValue");
				print '</td>';
				if (($action != 'editbarcode') && ! empty($user->rights->produit->creer) && $createbarcode) print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editbarcode&amp;id='.$object->id.'">'.img_edit($langs->trans('Edit'),1).'</a></td>';
				print '</tr></table>';
				print '</td><td colspan="2">';
				if ($action == 'editbarcode')
				{
					$tmpcode=isset($_POST['barcode'])?GETPOST('barcode'):$object->barcode;
					if (empty($tmpcode) && ! empty($modBarCodeProduct->code_auto)) $tmpcode=$modBarCodeProduct->getNextValue($object,$type);

					print '<form method="post" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'">';
					print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
					print '<input type="hidden" name="action" value="setbarcode">';
					print '<input type="hidden" name="barcode_type_code" value="'.$object->barcode_type_code.'">';
					print '<input size="40" class="maxwidthonsmartphone" type="text" name="barcode" value="'.$tmpcode.'">';
					print '&nbsp;<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
					print '</form>';
				}
				else
				{
					print $object->barcode;
				}
				print '</td></tr>'."\n";
			}

			// Accountancy sell code
			print '<tr><td class="nowrap">';
			print $langs->trans("ProductAccountancySellCode");
			print '</td><td colspan="2">';
			if (! empty($conf->accounting->enabled)) {
				print length_accountg($object->accountancy_code_sell);
			} else {
				print $object->accountancy_code_sell;
			}
			print '</td></tr>';

			// Accountancy buy code
			print '<tr><td class="nowrap">';
			print $langs->trans("ProductAccountancyBuyCode");
			print '</td><td colspan="2">';
			if (! empty($conf->accounting->enabled)) {
				print length_accountg($object->accountancy_code_buy);
			} else {
				print $object->accountancy_code_buy;
			}
			print '</td></tr>';

			// Status (to sell)
			/*
			print '<tr><td>'.$langs->trans("Status").' ('.$langs->trans("Sell").')</td><td colspan="2">';
			if (! empty($conf->use_javascript_ajax) && $user->rights->produit->creer && ! empty($conf->global->MAIN_DIRECT_STATUS_UPDATE)) {
				print ajax_object_onoff($object, 'status', 'tosell', 'ProductStatusOnSell', 'ProductStatusNotOnSell');
			} else {
				print $object->getLibStatut(2,0);
			}
			print '</td></tr>';

			// Status (to buy)
			print '<tr><td>'.$langs->trans("Status").' ('.$langs->trans("Buy").')</td><td colspan="2">';
			if (! empty($conf->use_javascript_ajax) && $user->rights->produit->creer && ! empty($conf->global->MAIN_DIRECT_STATUS_UPDATE)) {
				print ajax_object_onoff($object, 'status_buy', 'tobuy', 'ProductStatusOnBuy', 'ProductStatusNotOnBuy');
			} else {
				print $object->getLibStatut(2,1);
			}
			print '</td></tr>';
			*/

			// Batch number management (to batch)
			if (! empty($conf->productbatch->enabled)) {
				print '<tr><td>'.$langs->trans("ManageLotSerial").'</td><td colspan="2">';
				if (! empty($conf->use_javascript_ajax) && $user->rights->produit->creer && ! empty($conf->global->MAIN_DIRECT_STATUS_UPDATE)) {
					print ajax_object_onoff($object, 'status_batch', 'tobatch', 'ProductStatusOnBatch', 'ProductStatusNotOnBatch');
				} else {
					print $object->getLibStatut(0,2);
				}
				print '</td></tr>';
			}

			// Description
			print '<tr><td class="tdtop">'.$langs->trans("Description").'</td><td colspan="2">'.(dol_textishtml($object->description)?$object->description:dol_nl2br($object->description,1,true)).'</td></tr>';

			// Public URL
			print '<tr><td>'.$langs->trans("PublicUrl").'</td><td colspan="2">';
			print dol_print_url($object->url);
			print '</td></tr>';

			print '</table>';
			print '</div>';
			print '<div class="fichehalfright"><div class="ficheaddleft">';

			print '<div class="underbanner clearboth"></div>';
			print '<table class="border tableforfield" width="100%">';

			// Nature
			if($object->type!= Product::TYPE_SERVICE)
			{
				print '<tr><td class="titlefield">'.$langs->trans("Nature").'</td><td colspan="2">';
				print $object->getLibFinished();
				print '</td></tr>';
			}

			if ($object->isService())
			{
				// Duration
				print '<tr><td class="titlefield">'.$langs->trans("Duration").'</td><td colspan="2">'.$object->duration_value.'&nbsp;';
				if ($object->duration_value > 1)
				{
					$dur=array("h"=>$langs->trans("Hours"),"d"=>$langs->trans("Days"),"w"=>$langs->trans("Weeks"),"m"=>$langs->trans("Months"),"y"=>$langs->trans("Years"));
				}
				else if ($object->duration_value > 0)
				{
					$dur=array("h"=>$langs->trans("Hour"),"d"=>$langs->trans("Day"),"w"=>$langs->trans("Week"),"m"=>$langs->trans("Month"),"y"=>$langs->trans("Year"));
				}
				print (! empty($object->duration_unit) && isset($dur[$object->duration_unit]) ? $langs->trans($dur[$object->duration_unit]) : '')."&nbsp;";

				print '</td></tr>';
			}
			else
			{
				// Weight
				print '<tr><td class="titlefield">'.$langs->trans("Weight").'</td><td colspan="2">';
				if ($object->weight != '')
				{
					print $object->weight." ".measuring_units_string($object->weight_units,"weight");
				}
				else
				{
					print '&nbsp;';
				}
				print "</td></tr>\n";
				if (empty($conf->global->PRODUCT_DISABLE_LENGTH))
				{
					// Length
					print '<tr><td>'.$langs->trans("Length").'</td><td colspan="2">';
					if ($object->length != '')
					{
						print $object->length." ".measuring_units_string($object->length_units,"size");
					}
					else
					{
						print '&nbsp;';
					}
					print "</td></tr>\n";
				}
				if (empty($conf->global->PRODUCT_DISABLE_SURFACE))
				{
					// Surface
					print '<tr><td>'.$langs->trans("Surface").'</td><td colspan="2">';
					if ($object->surface != '')
					{
						print $object->surface." ".measuring_units_string($object->surface_units,"surface");
					}
					else
					{
						print '&nbsp;';
					}
					print "</td></tr>\n";
				}
				// Volume
				print '<tr><td>'.$langs->trans("Volume").'</td><td colspan="2">';
				if ($object->volume != '')
				{
					print $object->volume." ".measuring_units_string($object->volume_units,"volume");
				}
				else
				{
					print '&nbsp;';
				}
				print "</td></tr>\n";
			}

			// Unit
			if (! empty($conf->global->PRODUCT_USE_UNITS))
			{
				$unit = $object->getLabelOfUnit();

				print '<tr><td>'.$langs->trans('DefaultUnitToShow').'</td><td>';
				if ($unit !== '') {
					print $langs->trans($unit);
				}
				print '</td></tr>';
			}

			// Custom code
			if (empty($conf->global->PRODUCT_DISABLE_CUSTOM_INFO))
			{
				print '<tr><td>'.$langs->trans("CustomCode").'</td><td colspan="2">'.$object->customcode.'</td>';

				// Origin country code
				print '<tr><td>'.$langs->trans("CountryOrigin").'</td><td colspan="2">'.getCountry($object->country_id,0,$db).'</td>';
			}

			// Other attributes
			$parameters=array('colspan' => ' colspan="'.(2+(($showphoto||$showbarcode)?1:0)).'"');
			$reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
			if (empty($reshook) && ! empty($extrafields->attribute_label))
			{
				print $object->showOptionals($extrafields);
			}

			// Categories
			if($conf->categorie->enabled) {
				print '<tr><td valign="middle">'.$langs->trans("Categories").'</td><td colspan="3">';
				print $form->showCategories($object->id,'product',1);
				print "</td></tr>";
			}

			// Note private
			if (! empty($conf->global->MAIN_DISABLE_NOTES_TAB))
			{
				print '<!-- show Note --> '."\n";
				print '<tr><td class="tdtop">'.$langs->trans("NotePrivate").'</td><td colspan="'.(2+(($showphoto||$showbarcode)?1:0)).'">'.(dol_textishtml($object->note_private)?$object->note_private:dol_nl2br($object->note_private,1,true)).'</td></tr>'."\n";
				print '<!-- End show Note --> '."\n";
			}

			print "</table>\n";
			print '</div>';

			print '</div></div>';
			print '<div style="clear:both"></div>';

			dol_fiche_end();
		}

	}
	else if ($action != 'create')
	{
		exit;
	}
?>