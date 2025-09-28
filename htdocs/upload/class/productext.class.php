<?php
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';

class Productext extends Product
{
		/**
	 *	Update a record into database.
	 *  If batch flag is set to on, we create records into llx_product_batch
	 *
	 *	@param	int		$id         Id of product
	 *	@param  User	$user       Object user making update
	 *	@param	int		$notrigger	Disable triggers
	 *	@param	string	$action		Current action for hookmanager ('add' or 'update')
	 *	@return int         		1 if OK, -1 if ref already exists, -2 if other error
	 */
	function updateadd($id, $user, $notrigger=false, $action='update')
	{
		global $langs, $conf, $hookmanager;

		$error=0;

		// Check parameters
		if (! $this->label) $this->label = 'MISSING LABEL';

		$price_ht=0;
		$price_ttc=0;
		$price_min_ht=0;
		$price_min_ttc=0;
		if ($this->price_base_type == 'TTC' && $this->price_ttc > 0)
		{
			$price_ttc = price2num($this->price_ttc,'MU');
			$price_ht = price2num($this->price_ttc / (1 + ($this->tva_tx / 100)),'MU');
		}
		if ($this->price_base_type != 'TTC' && $this->price > 0)
		{
			$price_ht = price2num($this->price,'MU');
			$price_ttc = price2num($this->price * (1 + ($this->tva_tx / 100)),'MU');
		}
		if (($this->price_min_ttc > 0) && ($this->price_base_type == 'TTC'))
		{
			$price_min_ttc = price2num($this->price_min_ttc,'MU');
			$price_min_ht = price2num($this->price_min_ttc / (1 + ($this->tva_tx / 100)),'MU');
		}
		if (($this->price_min > 0) && ($this->price_base_type != 'TTC'))
		{
			$price_min_ht = price2num($this->price_min,'MU');
			$price_min_ttc = price2num($this->price_min * (1 + ($this->tva_tx / 100)),'MU');
		}


		// Clean parameters
		$this->ref = dol_string_nospecial(trim($this->ref));
		$this->label = trim($this->label);
		$this->description = trim($this->description);
		$this->note = (isset($this->note) ? trim($this->note) : null);
		$this->weight = price2num($this->weight);
		$this->weight_units = trim($this->weight_units);
		$this->length = price2num($this->length);
		$this->length_units = trim($this->length_units);
		$this->surface = price2num($this->surface);
		$this->surface_units = trim($this->surface_units);
		$this->volume = price2num($this->volume);
		$this->volume_units = trim($this->volume_units);
		if (empty($this->tva_tx))    			$this->tva_tx = 0;
		if (empty($this->tva_npr))    			$this->tva_npr = 0;
		if (empty($this->localtax1_tx))			$this->localtax1_tx = 0;
		if (empty($this->localtax2_tx))			$this->localtax2_tx = 0;
		if (empty($this->localtax1_type))		$this->localtax1_type = '0';
		if (empty($this->localtax2_type))		$this->localtax2_type = '0';
		if (empty($this->status))				$this->status = 0;
		if (empty($this->status_buy))			$this->status_buy = 0;

        if (empty($this->country_id))           $this->country_id = 0;
        if (empty($this->price_ht))           $this->price_ht = 0;
        if (empty($this->price_ttc))           $this->price_ttc = 0;

        // Barcode value
        $this->barcode=trim($this->barcode);

		$this->accountancy_code_buy = trim($this->accountancy_code_buy);
		$this->accountancy_code_sell= trim($this->accountancy_code_sell);


        $this->db->begin();

        // Check name is required and codes are ok or unique.
        // If error, this->errors[] is filled
        if ($action != 'add')
        {
        	$result = $this->verify();	// We don't check when update called during a create because verify was already done
        }

        if ($result >= 0)
        {
            if (empty($this->oldcopy))
            {
                $org=new self($this->db);
                $org->fetch($this->id);
                $this->oldcopy=$org;
            }
            
            // Test if batch management is activated on existing product
            // If yes, we create missing entries into product_batch
            if ($this->hasbatch() && !$this->oldcopy->hasbatch())
            {
                //$valueforundefinedlot = 'Undefined';  // In previous version, 39 and lower
                $valueforundefinedlot = '000000';

                dol_syslog("Flag batch of product id=".$this->id." is set to ON, so we will create missing records into product_batch");

                $this->load_stock();
                foreach ($this->stock_warehouse as $idW => $ObjW)   // For each warehouse where we have stocks defined for this product (for each lines in product_stock)
                {
                    $qty_batch = 0;
                    foreach ($ObjW->detail_batch as $detail)    // Each lines of detail in product_batch of the current $ObjW = product_stock
                    {
                        if ($detail->batch == $valueforundefinedlot || $detail->batch == 'Undefined') 
                        {
                            // We discard this line, we will create it later
                            $sqlclean="DELETE FROM ".MAIN_DB_PREFIX."product_batch WHERE batch in('Undefined', '".$valueforundefinedlot."') AND fk_product_stock = ".$ObjW->id;
                            $result = $this->db->query($sqlclean);
                            if (! $result)
                            {
                                dol_print_error($this->db);
                                exit;
                            }
                            continue;
                        }
                    
                        $qty_batch += $detail->qty;
                    }
                    // Quantities in batch details are not same as stock quantity,
                    // so we add a default batch record to complete and get same qty in parent and child table
                    if ($ObjW->real <> $qty_batch)
                    {
                        $ObjBatch = new Productbatch($this->db);
                        $ObjBatch->batch = $valueforundefinedlot; 
                        $ObjBatch->qty = ($ObjW->real - $qty_batch);
                        $ObjBatch->fk_product_stock = $ObjW->id;

                        if ($ObjBatch->create($user,1) < 0)
                        {
                            $error++;
                            $this->errors=$ObjBatch->errors;
                        }
                    }
                }
            }

	        // For automatic creation
	        if ($this->barcode == -1) $this->barcode = $this->get_barcode($this,$this->barcode_type_code);

			$sql = "UPDATE ".MAIN_DB_PREFIX."product";
			$sql.= " SET label = '" . $this->db->escape($this->label) ."'";
			$sql.= ", ref = '" . $this->db->escape($this->ref) ."'";
			$sql.= ", ref_ext = ".(! empty($this->ref_ext)?"'".$this->db->escape($this->ref_ext)."'":"null");
			$sql.= ", default_vat_code = ".($this->default_vat_code ? "'".$this->db->escape($this->default_vat_code)."'" : "null");
			$sql.= ", tva_tx = " . $this->tva_tx;
			$sql.= ", recuperableonly = " . $this->tva_npr;
			$sql.= ", localtax1_tx = " . $this->localtax1_tx;
			$sql.= ", localtax2_tx = " . $this->localtax2_tx;
			$sql.= ", localtax1_type = " . ($this->localtax1_type!=''?"'".$this->localtax1_type."'":"'0'");
			$sql.= ", localtax2_type = " . ($this->localtax2_type!=''?"'".$this->localtax2_type."'":"'0'");
				
			$sql.= ", barcode = ". (empty($this->barcode)?"null":"'".$this->db->escape($this->barcode)."'");
			$sql.= ", fk_barcode_type = ". (empty($this->barcode_type)?"null":$this->db->escape($this->barcode_type));

			$sql.= ", price_ttc = " . $price_ttc;
			$sql.= ", price = " . $price_ht;

			$sql.= ", tosell = " . $this->status;
			$sql.= ", tobuy = " . $this->status_buy;
			$sql.= ", tobatch = " . ((empty($this->status_batch) || $this->status_batch < 0) ? '0' : $this->status_batch);
			$sql.= ", finished = " . ((! isset($this->finished) || $this->finished < 0) ? "null" : (int) $this->finished);
			$sql.= ", weight = " . ($this->weight!='' ? "'".$this->weight."'" : 'null');
			$sql.= ", weight_units = " . ($this->weight_units!='' ? "'".$this->weight_units."'": 'null');
			$sql.= ", length = " . ($this->length!='' ? "'".$this->length."'" : 'null');
			$sql.= ", length_units = " . ($this->length_units!='' ? "'".$this->length_units."'" : 'null');
			$sql.= ", surface = " . ($this->surface!='' ? "'".$this->surface."'" : 'null');
			$sql.= ", surface_units = " . ($this->surface_units!='' ? "'".$this->surface_units."'" : 'null');
			$sql.= ", volume = " . ($this->volume!='' ? "'".$this->volume."'" : 'null');
			$sql.= ", volume_units = " . ($this->volume_units!='' ? "'".$this->volume_units."'" : 'null');
			$sql.= ", seuil_stock_alerte = " . ((isset($this->seuil_stock_alerte) && $this->seuil_stock_alerte != '') ? "'".$this->seuil_stock_alerte."'" : "null");
			$sql.= ", description = '" . $this->db->escape($this->description) ."'";
			$sql.= ", url = " . ($this->url?"'".$this->db->escape($this->url)."'":'null');
			$sql.= ", customcode = '" .        $this->db->escape($this->customcode) ."'";
	        $sql.= ", fk_country = " . ($this->country_id > 0 ? $this->country_id : 'null');
	        $sql.= ", note = ".(isset($this->note) ? "'" .$this->db->escape($this->note)."'" : 'null');
			$sql.= ", duration = '" . $this->db->escape($this->duration_value . $this->duration_unit) ."'";
			$sql.= ", accountancy_code_buy = '" . $this->db->escape($this->accountancy_code_buy)."'";
			$sql.= ", accountancy_code_sell= '" . $this->db->escape($this->accountancy_code_sell)."'";
			$sql.= ", desiredstock = " . ((isset($this->desiredstock) && $this->desiredstock != '') ? $this->desiredstock : "null");
			$sql.= ", cost_price = " . ($this->cost_price != '' ? $this->db->escape($this->cost_price) : 'null');
	        $sql.= ", fk_unit= " . (!$this->fk_unit ? 'NULL' : $this->fk_unit);
	        $sql.= ", price_autogen = " . (!$this->price_autogen ? 0 : 1);
			$sql.= ", fk_price_expression = ".($this->fk_price_expression != 0 ? $this->fk_price_expression : 'NULL');
			$sql.= ", fk_user_modif = ".($user->id > 0 ? $user->id : 'NULL');
			// stock field is not here because it is a denormalized value from product_stock.
			$sql.= " WHERE rowid = " . $id;

			dol_syslog(get_class($this)."::update", LOG_DEBUG);

			$resql=$this->db->query($sql);
			if ($resql)
			{
				$this->id = $id;

						if ($id > 0)
						{
							$this->id				= $id;
							$this->price			= $price_ht;
							$this->price_ttc		= $price_ttc;
							$this->price_min		= $price_min_ht;
							$this->price_min_ttc	= $price_min_ttc;

							$result = $this->_log_price($user);
							if ($result > 0)
							{
								//if ($this->update($id, $user, true, 'add') <= 0)
								//{
								//    $error++;
								//}
							}
							else
							{
								$error++;
							    $this->error=$this->db->lasterror();
							}
						}
				// Multilangs
				if (! empty($conf->global->MAIN_MULTILANGS))
				{
					if ( $this->setMultiLangs($user) < 0)
					{
						$this->error=$langs->trans("Error")." : ".$this->db->error()." - ".$sql;
						return -2;
					}
				}

				$action='update';

				// Actions on extra fields (by external module or standard code)
				$hookmanager->initHooks(array('productdao'));
				$parameters=array('id'=>$this->id);
				$reshook=$hookmanager->executeHooks('insertExtraFields',$parameters,$this,$action);    // Note that $action and $object may have been modified by some hooks
				if (empty($reshook))
				{
					if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED)) // For avoid conflicts if trigger used
					{
						$result=$this->insertExtraFields();
						if ($result < 0)
						{
							$error++;
						}
					}
				}
				else if ($reshook < 0) $error++;

				if (! $error && ! $notrigger)
				{
                    // Call trigger
                    $result=$this->call_trigger('PRODUCT_MODIFY',$user);
                    if ($result < 0) { $error++; }
                    // End call triggers
				}

				if (! $error && (is_object($this->oldcopy) && $this->oldcopy->ref != $this->ref))
				{
					// We remove directory
					if ($conf->product->dir_output)
					{
						$olddir = $conf->product->dir_output . "/" . dol_sanitizeFileName($this->oldcopy->ref);
						$newdir = $conf->product->dir_output . "/" . dol_sanitizeFileName($this->ref);
						if (file_exists($olddir))
						{
							//include_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';
							//$res = dol_move($olddir, $newdir);
							// do not use dol_move with directory
							$res = @rename($olddir, $newdir);
							if (! $res)
							{
							    $langs->load("errors");
								$this->error=$langs->trans('ErrorFailToRenameDir',$olddir,$newdir);
								$error++;
							}
						}
					}
				}

				if (! $error)
				{
					$this->db->commit();
					return 1;
				}
				else
				{
					$this->db->rollback();
					return -$error;
				}
			}
			else
			{
				if ($this->db->errno() == 'DB_ERROR_RECORD_ALREADY_EXISTS')
				{
					if (empty($conf->barcode->enabled)) $this->error=$langs->trans("Error")." : ".$langs->trans("ErrorProductAlreadyExists",$this->ref);
					else $this->error=$langs->trans("Error")." : ".$langs->trans("ErrorProductBarCodeAlreadyExists",$this->barcode);
					$this->errors[]=$this->error;
					$this->db->rollback();
					return -1;
				}
				else
				{
					$this->error=$langs->trans("Error")." : ".$this->db->error()." - ".$sql;
					$this->errors[]=$this->error;
					$this->db->rollback();
					return -2;
				}
			}
        }
        else
       {
            $this->db->rollback();
            dol_syslog(get_class($this)."::Update fails verify ".join(',',$this->errors), LOG_WARNING);
            return -3;
        }
	}
}
?>