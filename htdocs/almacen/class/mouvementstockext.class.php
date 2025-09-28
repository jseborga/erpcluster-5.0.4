<?php
require_once DOL_DOCUMENT_ROOT.'/product/stock/class/mouvementstock.class.php';


class MouvementStockext extends MouvementStock
{
	var $aIng = '';
	var $aSal = '';
	var $aIngentrepot='';
	var $aSalentrepot='';
	var $aBal = '';
	var $aMoving;
	var $aMovsal;
	var $aMovingppp;
	var $aMovsalppp;
	var $aMovsaldet;
	var $balance_peps;
	var $balance_ueps;
	var $value_peps;
	var $value_ueps;
	var $aSaldo;
	var $aMov;
	var $value_peps_min;
	var $aSales;
	var $lastPricepeps;
	var $lastPriceppp;
	var $actualPricepeps;
	var $actualPriceppp;
	var $aBalance;

	/**
	 *	Add a movement of stock (in one direction only)
	 *
	 *	@param		User	$user			User object
	 *	@param		int		$fk_product		Id of product
	 *	@param		int		$entrepot_id	Id of warehouse
	 *	@param		int		$qty			Qty of movement (can be <0 or >0 depending on parameter type)
	 *	@param		int		$type			Direction of movement:
	 *										0=input (stock increase by a stock transfer), 1=output (stock decrease after by a stock transfer),
	 *										2=output (stock decrease), 3=input (stock increase)
	 *                                      Note that qty should be > 0 with 0 or 3, < 0 with 1 or 2.
	 *	@param		int		$price			Unit price HT of product, used to calculate average weighted price (PMP in french). If 0, average weighted price is not changed.
	 *	@param		string	$label			Label of stock movement
	 *	@param		string	$inventorycode	Inventory code
	 *	@param		string	$datem			Force date of movement
	 *	@param		date	$eatby			eat-by date. Will be used if lot does not exists yet and will be created.
	 *	@param		date	$sellby			sell-by date. Will be used if lot does not exists yet and will be created.
	 *	@param		string	$batch			batch number
	 *	@param		boolean	$skip_batch		If set to true, stock movement is done without impacting batch record
	 * 	@param		int		$id_product_batch	Id product_batch (when skip_batch is false and we already know which record of product_batch to use)
	 *	@return		int						<0 if KO, 0 if fk_product is null, >0 if OK
	 */

	function _createadd($user, $fk_product, $entrepot_id, $qty, $type, $price=0, $label='', $inventorycode='', $datem='',$eatby='',$sellby='',$batch='',$skip_batch=false, $id_product_batch=0)
	{
		global $conf, $langs;

		require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
		require_once DOL_DOCUMENT_ROOT.'/product/stock/class/productlot.class.php';
		$error = 0;
		dol_syslog(get_class($this)."::_createadd start userid=$user->id, fk_product=$fk_product, warehouse_id=$entrepot_id, qty=$qty, type=$type, price=$price, label=$label, inventorycode=$inventorycode, datem=".$datem.", eatby=".$eatby.", sellby=".$sellby.", batch=".$batch.", skip_batch=".$skip_batch);

		// Clean parameters
		if (empty($price)) $price=0;
		$now=(! empty($datem) ? $datem : dol_now());

		// Check parameters
		if (empty($fk_product)) return 0;
		if ($eatby < 0)
		{
			$this->errors[]='ErrorBadValueForParameterEatBy';
			return -1;
		}
		if ($sellby < 0)
		{
			$this->errors[]='ErrorBadValueForParameterSellBy';
			return -1;
		}

		// Set properties of movement
		$this->product_id = $fk_product;
		$this->entrepot_id = $entrepot_id;
		$this->qty = $qty;
		$this->type = $type;

		$mvid = 0;

		$product = new Product($this->db);
		$result=$product->fetch($fk_product);
		if ($result < 0)
		{
			dol_print_error('',"Failed to fetch product");
			return -1;
		}

		$this->db->begin();

		$product->load_stock();

		// Test if product require batch data. If yes, and there is not, we throw an error.
		if (! empty($conf->productbatch->enabled) && $product->hasbatch() && ! $skip_batch)
		{
			if (empty($batch))
			{
				$this->errors[]=$langs->trans("ErrorTryToMakeMoveOnProductRequiringBatchData", $product->name);
				dol_syslog("Try to make a movement of a product with status_batch on without any batch data");

				$this->db->rollback();
				return -2;
			}
			// Check table llx_product_lot from batchnumber for same product
			// If found and eatby/sellby defined into table and provided and differs, return error
			// If found and eatby/sellby defined into table and not provided, we take value from table
			// If found and eatby/sellby not defined into table and provided, we update table
			// If found and eatby/sellby not defined into table and not provided, we do nothing
			// If not found, we add record
			$sql = "SELECT pb.rowid, pb.batch, pb.eatby, pb.sellby FROM ".MAIN_DB_PREFIX."product_lot as pb";
			$sql.= " WHERE pb.fk_product = ".$fk_product." AND pb.batch = '".$this->db->escape($batch)."'";
			dol_syslog(get_class($this)."::_createadd scan serial for this product to check if eatby and sellby match", LOG_DEBUG);
			$resql = $this->db->query($sql);
			if ($resql)
			{
				$num = $this->db->num_rows($resql);
				$i=0;
				if ($num > 0)
				{
					while ($i < $num)
					{
						$obj = $this->db->fetch_object($resql);
						if ($obj->eatby)
						{
							if ($eatby)
							{
								$tmparray=dol_getdate($eatby, true);
								$eatbywithouthour=dol_mktime(0, 0, 0, $tmparray['mon'], $tmparray['mday'], $tmparray['year']);
								if ($this->db->jdate($obj->eatby) != $eatby && $this->db->jdate($obj->eatby) != $eatbywithouthour)    // We test date without hours and with hours for backward compatibility
								{
									// If found and eatby/sellby defined into table and provided and differs, return error
									$this->errors[]=$langs->trans("ThisSerialAlreadyExistWithDifferentDate", $batch, dol_print_date($this->db->jdate($obj->eatby), 'dayhour'), dol_print_date($eatby, 'dayhour'));
									dol_syslog("ThisSerialAlreadyExistWithDifferentDate batch=".$batch.", eatby found into product_lot = ".$obj->eatby." = ".dol_print_date($this->db->jdate($obj->eatby), 'dayhourrfc')." so eatbywithouthour = ".$eatbywithouthour." = ".dol_print_date($eatbywithouthour)." - eatby provided = ".$eatby." = ".dol_print_date($eatby, 'dayhourrfc'), LOG_ERR);
									$this->db->rollback();
									return -3;
								}
							}
							else
							{
								$eatby = $obj->eatby; // If found and eatby/sellby defined into table and not provided, we take value from table
							}
						}
						else
						{
							if ($eatby) // If found and eatby/sellby not defined into table and provided, we update table
							{
								$productlot = new Productlot($this->db);
								$result = $productlot->fetch($obj->rowid);
								$productlot->eatby = $eatby;
								$result = $productlot->update($user);
								if ($result <= 0)
								{
									$this->error = $productlot->error;
									$this->errors = $productlot->errors;
									$this->db->rollback();
									return -5;
								}
							}
						}
						if ($obj->sellby)
						{
							if ($sellby)
							{
								$tmparray=dol_getdate($sellby, true);
								$sellbywithouthour=dol_mktime(0, 0, 0, $tmparray['mon'], $tmparray['mday'], $tmparray['year']);
								if ($this->db->jdate($obj->sellby) != $sellby && $this->db->jdate($obj->sellby) != $sellbywithouthour)    // We test date without hours and with hours for backward compatibility
								{
									// If found and eatby/sellby defined into table and provided and differs, return error
									$this->errors[]=$langs->trans("ThisSerialAlreadyExistWithDifferentDate", $batch, dol_print_date($this->db->jdate($obj->sellby)), dol_print_date($sellby));
									dol_syslog($langs->transnoentities("ThisSerialAlreadyExistWithDifferentDate", $batch, dol_print_date($this->db->jdate($obj->sellby)), dol_print_date($sellby)), LOG_ERR);
									$this->db->rollback();
									return -3;
								}
							}
							else
							{
								$sellby = $obj->sellby; // If found and eatby/sellby defined into table and not provided, we take value from table
							}
						}
						else
						{
							if ($sellby) // If found and eatby/sellby not defined into table and provided, we update table
							{
								$productlot = new Productlot($this->db);
								$result = $productlot->fetch($obj->rowid);
								$productlot->sellby = $sellby;
								$result = $productlot->update($user);
								if ($result <= 0)
								{
									$this->error = $productlot->error;
									$this->errors = $productlot->errors;
									$this->db->rollback();
									return -5;
								}
							}
						}

						$i++;
					}
				}
				else   // If not found, we add record
				{
					$productlot = new Productlot($this->db);
					$productlot->fk_product = $fk_product;
					$productlot->batch = $batch;
					// If we are here = first time we manage this batch, so we used dates provided by users to create lot
					$productlot->eatby = $eatby;
					$productlot->sellby = $sellby;
					$result = $productlot->create($user);
					if ($result <= 0)
					{
						$this->error = $productlot->error;
						$this->errors = $productlot->errors;
						$this->db->rollback();
						return -4;
					}
				}
			}
			else
			{
				dol_print_error($this->db);
				$this->db->rollback();
				return -1;
			}
		}

		// Define if we must make the stock change (If product type is a service or if stock is used also for services)
		$movestock=0;
		if ($product->type != Product::TYPE_SERVICE || ! empty($conf->global->STOCK_SUPPORTS_SERVICES)) $movestock=1;

		// Check if stock is enough when qty is < 0
		// Note that qty should be > 0 with type 0 or 3, < 0 with type 1 or 2.
		if ($movestock && $qty < 0 && empty($conf->global->STOCK_ALLOW_NEGATIVE_TRANSFER))
		{
			if (! empty($conf->productbatch->enabled) && $product->hasbatch() && ! $skip_batch)
			{
				$foundforbatch=0;
				$qtyisnotenough=0;
				foreach($product->stock_warehouse[$entrepot_id]->detail_batch as $batchcursor => $prodbatch)
				{
					if ($batch != $batchcursor) continue;
					$foundforbatch=1;
					if ($prodbatch->qty < abs($qty)) $qtyisnotenough=1;
					break;
				}
				if (! $foundforbatch || $qtyisnotenough)
				{
					$langs->load("stocks");
					$this->error = $langs->trans('qtyToTranferLotIsNotEnough');
					$this->errors[] = $langs->trans('qtyToTranferLotIsNotEnough');
					$this->db->rollback();
					return -8;
				}
			}
			else
			{
				if (empty($product->stock_warehouse[$entrepot_id]->real) || $product->stock_warehouse[$entrepot_id]->real < abs($qty))
				{
					$langs->load("stocks");
					$this->error = $langs->trans('qtyToTranferIsNotEnough');
					$this->errors[] = $langs->trans('qtyToTranferIsNotEnough');
					$this->db->rollback();
					return -8;
				}
			}
		}

		if ($movestock && $entrepot_id > 0)	// Change stock for current product, change for subproduct is done after
		{
			if(!empty($this->origin)) {			// This is set by caller for tracking reason
				$origintype = $this->origin->element;
				$fk_origin = $this->origin->id;
			} else {
				$origintype = '';
				$fk_origin = 0;
			}

			$sql = "INSERT INTO ".MAIN_DB_PREFIX."stock_mouvement(";
			$sql.= " datem, fk_product, batch, eatby, sellby,";
			$sql.= " fk_entrepot, value, type_mouvement, fk_user_author, label, inventorycode, price, fk_origin, origintype";
			$sql.= ")";
			$sql.= " VALUES ('".$this->db->idate($now)."', ".$this->product_id.", ";
			$sql.= " ".($batch?"'".$batch."'":"null").", ";
			$sql.= " ".($eatby?"'".$this->db->idate($eatby)."'":"null").", ";
			$sql.= " ".($sellby?"'".$this->db->idate($sellby)."'":"null").", ";
			$sql.= " ".$this->entrepot_id.", ".$this->qty.", ".$this->type.",";
			$sql.= " ".$user->id.",";
			$sql.= " '".$this->db->escape($label)."',";
			$sql.= " ".($inventorycode?"'".$this->db->escape($inventorycode)."'":"null").",";
			$sql.= " '".price2num($price)."',";
			$sql.= " '".$fk_origin."',";
			$sql.= " '".$origintype."'";
			$sql.= ")";

			dol_syslog(get_class($this)."::_createadd insert record into stock_mouvement", LOG_DEBUG);
			$resql = $this->db->query($sql);
			if ($resql)
			{
				$mvid = $this->db->last_insert_id(MAIN_DB_PREFIX."stock_mouvement");
				$this->id = $mvid;
			}
			else
			{
				$this->errors[]=$this->db->lasterror();
				$error = -1;
			}

			// Define current values for qty and pmp
			$oldqty=$product->stock_reel;
			$oldpmp=$product->pmp;
			$oldqtywarehouse=0;

			// Test if there is already a record for couple (warehouse / product)
			$alreadyarecord = 0;
			if (! $error)
			{
				$sql = "SELECT rowid, reel FROM ".MAIN_DB_PREFIX."product_stock";
				$sql.= " WHERE fk_entrepot = ".$entrepot_id." AND fk_product = ".$fk_product;		// This is a unique key

				dol_syslog(get_class($this)."::_createadd check if a record already exists in product_stock", LOG_DEBUG);
				$resql=$this->db->query($sql);
				if ($resql)
				{
					$obj = $this->db->fetch_object($resql);
					if ($obj)
					{
						$alreadyarecord = 1;
						$oldqtywarehouse = $obj->reel;
						$fk_product_stock = $obj->rowid;
					}
					$this->db->free($resql);
				}
				else
				{
					$this->errors[]=$this->db->lasterror();
					$error = -2;
				}
			}

			// Calculate new PMP.
			$newpmp=0;
			if (! $error)
			{
				// Note: PMP is calculated on stock input only (type of movement = 0 or 3). If type == 0 or 3, qty should be > 0.
				// Note: Price should always be >0 or 0. PMP should be always >0 (calculated on input)
				if (($type == 0 || $type == 3) && $price > 0)
				{
					$oldqtytouse=($oldqty >= 0?$oldqty:0);
					// We make a test on oldpmp>0 to avoid to use normal rule on old data with no pmp field defined
					if ($oldpmp > 0) $newpmp=price2num((($oldqtytouse * $oldpmp) + ($qty * $price)) / ($oldqtytouse + $qty), 'MU');
					else
					{
						$newpmp=$price; // For this product, PMP was not yet set. We set it to input price.
					}
					//print "oldqtytouse=".$oldqtytouse." oldpmp=".$oldpmp." oldqtywarehousetouse=".$oldqtywarehousetouse." ";
					//print "qty=".$qty." newpmp=".$newpmp;
					//exit;
				}
				else if ($type == 1 || $type == 2)
				{
					// After a stock decrease, we don't change value of PMP for product.
					$newpmp = $oldpmp;
				}
				else
				{
					$newpmp = $oldpmp;
				}
			}
			$nDecimal = $conf->global->ALMACEN_NUMBER_DECIMAL_PRODUCT_BALANCE;
			// Update stock quantity
			if (! $error)
			{
				if ($alreadyarecord > 0)
				{
					$sql = "UPDATE ".MAIN_DB_PREFIX."product_stock SET reel = reel + ".($nDecimal>0?price2num($qty,$nDecimal):$qty);
					$sql.= " WHERE fk_entrepot = ".$entrepot_id." AND fk_product = ".$fk_product;
				}
				else
				{
					$sql = "INSERT INTO ".MAIN_DB_PREFIX."product_stock";
					$sql.= " (reel, fk_entrepot, fk_product) VALUES ";
					$sql.= " (".($nDecimal>0?price2num($qty,$nDecimal):$qty).", ".$entrepot_id.", ".$fk_product.")";
				}

				dol_syslog(get_class($this)."::_createadd update stock value", LOG_DEBUG);
				$resql=$this->db->query($sql);
				if (! $resql)
				{
					$this->errors[]=$this->db->lasterror();
					$error = -3;
				}
				else if (empty($fk_product_stock))
				{
					$fk_product_stock = $this->db->last_insert_id(MAIN_DB_PREFIX."product_stock");
				}

			}

			// Update detail stock for batch product
			if (! $error && ! empty($conf->productbatch->enabled) && $product->hasbatch() && ! $skip_batch)
			{
				if ($id_product_batch > 0)
				{
					$result=$this->createBatch($id_product_batch, $qty);
				}
				else
				{
					$param_batch=array('fk_product_stock' =>$fk_product_stock, 'batchnumber'=>$batch);
					$result=$this->createBatch($param_batch, $qty);
				}
				if ($result<0) $error++;
			}

			// Update PMP and denormalized value of stock qty at product level
			if (! $error)
			{
				// $sql = "UPDATE ".MAIN_DB_PREFIX."product SET pmp = ".$newpmp.", stock = ".$this->db->ifsql("stock IS NULL", 0, "stock") . " + ".$qty;
				// $sql.= " WHERE rowid = ".$fk_product;
				// Update pmp + denormalized fields because we change content of produt_stock. Warning: Do not use "SET p.stock", does not works with pgsql
				$sql = "UPDATE ".MAIN_DB_PREFIX."product as p SET pmp = ".$newpmp.", ";
				$sql.= " stock=(SELECT SUM(ps.reel) FROM ".MAIN_DB_PREFIX."product_stock as ps WHERE ps.fk_product = p.rowid)";
				$sql.= " WHERE rowid = ".$fk_product;

				dol_syslog(get_class($this)."::_createadd update AWP", LOG_DEBUG);
				$resql=$this->db->query($sql);
				if (! $resql)
				{
					$this->errors[]=$this->db->lasterror();
					$error = -4;
				}
			}

			// If stock is now 0, we can remove entry into llx_product_stock, but only if there is no child lines into llx_product_batch (detail of batch, because we can imagine
			// having a lot1/qty=X and lot2/qty=-X, so 0 but we must not loose repartition of different lot.
			$sql="DELETE FROM ".MAIN_DB_PREFIX."product_stock WHERE reel = 0 AND rowid NOT IN (SELECT fk_product_stock FROM ".MAIN_DB_PREFIX."product_batch as pb)";
			$resql=$this->db->query($sql);
			// We do not test error, it can fails if there is child in batch details
		}

		// Add movement for sub products (recursive call)
		if (! $error && ! empty($conf->global->PRODUIT_SOUSPRODUITS) && empty($conf->global->INDEPENDANT_SUBPRODUCT_STOCK))
		{
			$error = $this->_createSubProduct($user, $fk_product, $entrepot_id, $qty, $type, 0, $label, $inventorycode);	// we use 0 as price, because pmp is not changed for subproduct
		}

		if ($movestock && ! $error)
		{
			// Call trigger
			$result=$this->call_trigger('STOCK_MOVEMENT',$user);
			if ($result < 0) $error++;
			// End call triggers
		}

		if (! $error)
		{
			$this->db->commit();
			return $mvid;
		}
		else
		{
			$this->db->rollback();
			dol_syslog(get_class($this)."::_createadd error code=".$error, LOG_ERR);
			return -6;
		}
	}


	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND', $filterstatic = '',$lView=false,$ordernew=0)
	{
		$sql = " SELECT ";
		$sql.= " t.rowid, datem, fk_product, batch, eatby, sellby,";
		$sql.= " fk_entrepot, value, type_mouvement, fk_user_author, label, inventorycode, price, fk_origin, origintype";
		$sql.= " , a.fk_stock_mouvement, a.balance_peps, a.balance_ueps, a.value_peps, a.value_ueps, a.qty, a.value_peps_adq, a.value_ueps_adq, a.fk_parent_line ";
		$sql.= " FROM ".MAIN_DB_PREFIX."stock_mouvement AS t ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."stock_mouvement_add AS a ON a.fk_stock_mouvement = t.rowid ";

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' WHERE ' . implode(' '.$filtermode.' ', $sqlwhere);
		}

		if ($filterstatic) $sql.= $filterstatic;

		if ($ordernew)
		{
			if (!empty($sortfield))
				$sql.= " ORDER BY ".$sortfield;
		}
		else
		{
			if (!empty($sortfield)) {
				$sql .= $this->db->order($sortfield,$sortorder);
			}
		}
		if (!empty($limit)) {
			$sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}

		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new MouvementstockLine();

				$line->id = $obj->rowid;

				$line->fk_product = $obj->fk_product;
				$line->fk_entrepot = $obj->fk_entrepot;
				$line->value = $obj->value;
				$line->price = $obj->price;
				$line->type_mouvement = $obj->type_mouvement;
				$line->fk_user_author = $obj->fk_user_author;
				$line->label = $obj->label;
				$line->fk_origin = $obj->fk_origin;
				$line->origintype = $obj->origintype;
				$line->batch = $obj->batch;
				$line->eatby = $this->db->jdate($obj->eatby);
				$line->sellby = $this->db->jdate($obj->sellby);
				$line->datem = $this->db->jdate($obj->datem);
				$line->tms = $this->db->jdate($obj->tms);
				$line->status = $obj->status;

				$line->qty = $obj->qty;
				$line->fk_stock_mouvement = $obj->fk_stock_mouvement;
				$line->fk_parent_line = $obj->fk_parent_line;
				$line->balance_peps = $obj->balance_peps;
				$line->balance_ueps = $obj->balance_ueps;
				$line->value_peps = $obj->value_peps;
				$line->value_ueps = $obj->value_ueps;
				$line->value_peps_adq = $obj->value_peps_adq;
				$line->value_ueps_adq = $obj->value_ueps_adq;

				if ($lView)
				{
					if ($num == 1) $this->fetch($obj->rowid);
				}

				$this->lines[$line->id] = $line;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}

	//metodo de valuacion
	function method_valuation($entrepotid=0,$fechaIni=0,$fk_product=0,$selsaldo=0)
	{
		global $conf;
		//saldos anterior
		$aDate = dol_getdate($fechaIni);
		if ($conf->global->ALMACEN_FILTER_YEAR)
			$year = $_SESSION['period_year'];
		//if (empty($year)) $year = date('Y');
		$dateini = $this->db->idate($fechaIni);
		if ($entrepotid)
			$filterstatic = " AND fk_entrepot = ".$entrepotid;

		//la valuacion se hace con la fecha actual
		//verificar funcionamiento con la condicion
		if ($fechaIni > 0)
			$filterstatic.= " AND datem <= '".$dateini."'";

		//SE ESTA VERIFICANDO POR RQC
		//if ($conf->global->ALMACEN_FILTER_YEAR)
		//	$filterstatic.= " AND a.period_year = ".$year;
		if (!empty($fk_product))
			$filterstatic.= " AND fk_product = ".$fk_product;
		$method = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;
		if (empty($conf->global->ALMACEN_METHOD_VALUATION_INVENTORY))
		{
			$sortorder = '';
			$sortfield = '';
		}
		elseif($conf->global->ALMACEN_METHOD_VALUATION_INVENTORY==1)
		{
			$sortorder = 'ASC|ASC';
			$sortfield = 'fk_product ASC,datem ASC, rowid ASC ';
		}
		elseif($conf->global->ALMACEN_METHOD_VALUATION_INVENTORY==2)
		{
			$sortorder = 'ASC|DESC';
			$sortfield = 'fk_product ASC,datem DESC, rowid DESC ';
		}
		if ($selsaldo)
			$filterstatic.= " AND balance_peps>0";
		$res = $this->fetchAll($sortorder, $sortfield, 0, 0, array(1=>1), 'AND', $filterstatic,false,1);
		if ($res <0)
			return -1;
		//armamos los precios segun
		$lines = $this->lines;
		$this->aIng = array();
		$this->aSal = array();
		$this->aBal = array();
		$num = count($lines);
		foreach ((array) $lines AS $j => $line)
		{
			if ($line->type_mouvement == 3 || ($line->type_mouvement == 0 && $line->value >0))
			{
				$this->aIng[$j]= $line;
			}
			if ($line->type_mouvement == 1 || $line->type_mouvement == 2 || ($line->type_mouvement == 0 && $line->value <0))
			{
				$this->aSal[$j]= $line;
			}
		}
		return $num;
	}

	//funcion para determinar saldos fisico y valorado
	function saldoanterior($entrepotid=0,$fechaIni,$fk_product=0)
	{
		global $conf;
		$aDate = dol_getdate($fechaIni);
		if ($conf->global->ALMACEN_FILTER_YEAR)
			$year = $_SESSION['period_year'];
		//$dateini = $this->db->idate($fechaIni);
		//saldos anterior
		$sql  = "SELECT p.rowid, sm.value AS saldo, sm.price, sm.type_mouvement, ";
		$sql.= " sma.value_peps, sma.value_ueps, sma.balance_peps, sma.balance_ueps, sma.fk_stock_mouvement AS rowidsma ";
		$sql.= " FROM ".MAIN_DB_PREFIX."stock_mouvement AS sm";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product AS p ON sm.fk_product = p.rowid ";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."stock_mouvement_add AS sma ON sma.fk_stock_mouvement = sm.rowid";
		$sql.= " WHERE 1=1 ";
		if ($entrepotid > 0)
			$sql.= " AND fk_entrepot = ".$entrepotid;
		if (! empty($fechaIni))
		{
			//$sql .= " AND sm.datem <= ".$this->db->idate($fechaIni);
			$sql .= " AND sm.datem <= ".$this->db->idate($fechaIni);

		}
		//if ($conf->global->ALMACEN_FILTER_YEAR)
		//	$sql .= " AND year(sm.datem) = ".$year;

		if (!empty($fk_product))
			$sql.= " AND sm.fk_product = ".$fk_product;

		$sql.= " ORDER BY sm.datem ASC, sm.rowid ASC ";
		$result = $this->db->query($sql);

		$this->aSaldo = array();
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;
			if ($num)
			{
				while ($i < $num)
				{
					//actualizando totales
					$objp = $this->db->fetch_object($result);
					$this->aSaldo[$objp->rowid]['qty']+=$objp->saldo;
					if ($objp->type_mouvement == 3 || ($objp->type_mouvement == 0 && $objp->saldo >0))
					{
						if (empty($this->value_peps_min))
						{
							if ($objp->balance_peps>0) $this->value_peps_min = $objp->value_peps;
						}
						if ($objp->value_peps > 0) $this->lastPricepeps[$objp->rowid] = $objp->value_peps;
						if ($objp->price > 0)$this->lastPriceppp[$objp->rowid] = $objp->price;
						$this->aSaldo[$objp->rowid]['idreg'] =$objp->rowidsma;
						$this->aSaldo[$objp->rowid]['value_ppp'] +=$objp->saldo*$objp->price;
						$this->aSaldo[$objp->rowid]['value_peps']+=$objp->saldo*$objp->value_peps;
						$this->aSaldo[$objp->rowid]['value_ueps']+=$objp->saldo*$objp->value_ueps;
					}
					if ($objp->type_mouvement == 1 || $objp->type_mouvement == 2 || ($objp->type_mouvement == 0 && $objp->saldo <0))
					{
						$this->aSaldo[$objp->rowid]['idreg'] =$objp->rowidsma;
						$this->aSaldo[$objp->rowid]['value_ppp'] +=$objp->saldo*$objp->price;
						$this->aSaldo[$objp->rowid]['value_peps']+=$objp->saldo*$objp->value_peps;
						$this->aSaldo[$objp->rowid]['value_ueps']+=$objp->saldo*$objp->value_ueps;
					}
					$i++;
				}
			}
			else
			{
				$this->aSaldo = array();
			}
			$this->db->free($result);
			return $num;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}


	//funcion para determinar saldos fisico y valorado
	function saldo_anterior($entrepotid=0,$fechaIni,$fk_product=0)
	{
		global $conf;
		$aDate = dol_getdate($fechaIni);
		if ($conf->global->ALMACEN_FILTER_YEAR) $year = $_SESSION['period_year'];
		if (empty($year)) $year = date('Y');
		//$dateini = $this->db->idate($fechaIni);
		//saldos anterior
		$sql  = "SELECT p.rowid, sm.value AS saldo, sm.price, sm.type_mouvement, ";
		$sql.= " sma.value_peps, sma.value_ueps, sma.balance_peps, sma.balance_ueps ";
		$sql.= " FROM ".MAIN_DB_PREFIX."stock_mouvement AS sm";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product AS p ON sm.fk_product = p.rowid ";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."stock_mouvement_add AS sma ON sma.fk_stock_mouvement = sm.rowid";
		$sql.= " WHERE 1 ";
		if ($entrepotid>0)
			$sql.= " AND fk_entrepot = ".$entrepotid;
		if (! empty($fechaIni))
		{
			//$sql .= " AND sm.datem <= ".$this->db->idate($fechaIni);
			$sql .= " AND sm.datem <= ".$this->db->idate($fechaIni);

		}
		if ($conf->global->ALMACEN_FILTER_YEAR)
			$sql .= " AND year(sm.datem) = ".$year;

		if (!empty($fk_product))
			$sql.= " AND sm.fk_product = ".$fk_product;
		$result = $this->db->query($sql);

		$this->aSaldo = array();
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;
			if ($num)
			{
				while ($i < $num)
				{
					//actualizando totales
					$objp = $this->db->fetch_object($result);
					$this->aSaldo[$objp->rowid]['qty']+=$objp->saldo;
					if ($objp->type_mouvement == 3 || ($objp->type_mouvement == 0 && $objp->saldo >0))
					{
						if (empty($this->value_peps_min))
						{
							if ($objp->balance_peps>0)
								$this->value_peps_min = $objp->value_peps;
						}
						$this->aSaldo[$objp->rowid]['value_ppp'] +=$objp->saldo*$objp->price;
						$this->aSaldo[$objp->rowid]['value_peps']+=$objp->saldo*$objp->value_peps;
						$this->aSaldo[$objp->rowid]['value_ueps']+=$objp->saldo*$objp->value_ueps;
					}
					if ($objp->type_mouvement == 1 || $objp->type_mouvement == 2 || ($objp->type_mouvement == 0 && $objp->value <0))
					{
						$this->aSaldo[$objp->rowid]['value_ppp'] +=$objp->saldo*$objp->price;
						$this->aSaldo[$objp->rowid]['value_peps']+=$objp->saldo*$objp->value_peps;
						$this->aSaldo[$objp->rowid]['value_ueps']+=$objp->saldo*$objp->value_ueps;
					}
					$i++;
				}
			}
			else
			{
				$this->aSaldo = array();
			}
			$this->db->free($result);
			return $num;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}

	/**
	 * [mouvement_period description]
	 * @param  integer   $entrepotid  [description]
	 * @param  [date]    $fechaIni    [description]
	 * @param  [date]    $fechaFin    [description]
	 * @param  integer   $fk_product  [description]
	 * @param  string    $idsproduct  [description]
	 * @param  integer   $processtype [0=no excluye nada del campo process_type, 1=Filtra los process_type = 0]
	 * @return [type]                 [description]
	 */
	function mouvement_period($entrepotid=0,$fechaIni,$fechaFin,$fk_product=0,$idsproduct='',$processtype=0)
	{
		global $conf,$user;
		if ($conf->global->ALMACEN_FILTER_YEAR)
		{
			$year = $_SESSION['period_year'];
			if (empty($year)) $year = date('Y');
		}
		$sql = "SELECT m.rowid AS fk, p.rowid, p.ref as product_ref, p.label as produit, p.fk_product_type as type,";
		$sql.= " m.rowid as mid, m.value AS saldo, m.datem, m.fk_user_author, m.label, m.fk_origin, m.origintype,m.datem, m.type_mouvement, m.price, m.fk_entrepot ";
		//$sql.= " u.login ";
		$sql.= " , ma.value_peps, ma.value_ueps, ma.fk_parent_line, ma.balance_peps, ma.balance_ueps, ma.qty AS qtyadd ";
		$sql.= " FROM (".MAIN_DB_PREFIX."stock_mouvement as m)";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product as p ON m.fk_product = p.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."stock_mouvement_add AS ma ON ma.fk_stock_mouvement = m.rowid";
		//$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."user as u ON m.fk_user_author = u.rowid";
		$sql.= " WHERE 1 ";
		if (empty($conf->global->STOCK_SUPPORTS_SERVICES)) $sql.= " AND p.fk_product_type = 0";
		if ($entrepotid>0)
		{
			$sql.= " AND m.fk_entrepot ='".$entrepotid."'";
		}
		if ($fk_product>0)
		{
			$sql.= " AND p.rowid =".$fk_product;
		}
		elseif ($idsproduct)
		{
			$sql.= " AND p.rowid IN( ".$idsproduct.")";
		}
		//$sql .= " AND UNIX_TIMESTAMP(m.datem) BETWEEN ".$fechaIni." AND ".$fechaFin;
		$sql .= " AND m.datem BETWEEN ".$this->db->idate($fechaIni)." AND ".$this->db->idate($fechaFin);

		//$sql.= " AND m.datem BETWEEN '".$this->db->idate($fechaIni)."' AND '".$this->db->idate($fechaFin)."'";

		//if ($conf->global->ALMACEN_FILTER_YEAR)
		//	$sql .= " AND year(m.datem) = ".$year;
		if ($processtype<>0)
			$sql.= " AND process_type = 0";
		$sql.= " ORDER BY m.datem ASC, m.rowid ASC ";

		$resql = $this->db->query($sql);
		$productidselected = $obj->rowid;
		$arrayofuniqueproduct=array();
		$arrayofuniqueproduct[$obj->rowid]=$obj->rowid;
		$input = 0;
		$output = 0;
		$this->aIng = array();
		$this->aSal = array();
		$this->aMoving = array();
		$this->aMovsal = array();
		$this->aSalentrepot = array();
		if ($resql)
		{
			$i = 0;
			$num = $this->db->num_rows($resql);
			while ($i < $num)
			{
				$objp = $this->db->fetch_object($resql);
				//actualizando totales
					//$this->aMov[$objp->rowid]['qty']+$objp->saldo;

				if ($objp->type_mouvement == 3 || ($objp->type_mouvement == 0 && $objp->saldo >0))
				{
					if ($objp->value_peps>0)$this->actualPricepeps[$objp->rowid] = $objp->value_peps;
					if ($objp->price>0)$this->actualPriceppp[$objp->rowid] = $objp->price;

					$this->aIng[$objp->rowid]['qty']+=$objp->saldo;
					$this->aIng[$objp->rowid]['value_ppp']+=$objp->saldo*$objp->price;
					$this->aIng[$objp->rowid]['value_peps']+=$objp->saldo*$objp->value_peps;
					$this->aIng[$objp->rowid]['value_ueps']+=$objp->saldo*$objp->value_ueps;
					$this->aIng[$objp->rowid]['datem']= $this->db->jdate($objp->datem);
					$this->lastPricepeps[$objp->rowid] = $objp->value_peps;
					$this->lastPriceppp[$objp->rowid] = $objp->price;
					$this->aMoving[$objp->rowid][$objp->fk]['qty']+= $objp->saldo;
					$this->aMoving[$objp->rowid][$objp->fk]['qtypeps']= $objp->qtyadd;
					$this->aMoving[$objp->rowid][$objp->fk]['qtyueps']= $objp->qtyadd;
					$this->aMoving[$objp->rowid][$objp->fk]['datem']= $this->db->jdate($objp->datem);
					//precios
					$this->aMoving[$objp->rowid][$objp->fk]['price_ppp']= $objp->price;
					$this->aMoving[$objp->rowid][$objp->fk]['value_ppp']=$objp->saldo*$objp->price;
					$this->aMoving[$objp->rowid][$objp->fk]['price_peps']= $objp->value_peps;
					$this->aMoving[$objp->rowid][$objp->fk]['value_peps']=$objp->balance_peps*$objp->value_peps;
					$this->aMoving[$objp->rowid][$objp->fk]['price_ueps']= $objp->value_eeps;
					$this->aMoving[$objp->rowid][$objp->fk]['value_ueps']=$objp->balance_ueps*$objp->value_ueps;


					$this->aMovdet[$objp->rowid][$objp->fk]['datem']= $this->db->jdate($objp->datem);
					$this->aMovdet[$objp->rowid][$objp->fk]['type_mouvement']= 3;
					$this->aMovdet[$objp->rowid][$objp->fk]['qty']= $objp->saldo;
				}

				if ($objp->type_mouvement == 1 || $objp->type_mouvement == 2 || ($objp->type_mouvement == 0 && $objp->saldo < 0) )
				{
					$this->aSal[$objp->rowid]['qty']+=$objp->saldo;
					$this->aSal[$objp->rowid]['value_ppp']+=$objp->saldo*$objp->price;
					$this->aSal[$objp->rowid]['value_peps']+=$objp->saldo*$objp->value_peps;
					$this->aSal[$objp->rowid]['value_ueps']+=$objp->saldo*$objp->value_ueps;

					$this->aSalentrepot[$objp->rowid][$objp->fk_entrepot] = $objp->fk_entrepot;


					$this->aMovsal[$objp->rowid][($objp->fk_parent_line?$objp->fk_parent_line:0)]['qty']+= $objp->saldo;

					$this->aMovsal[$objp->rowid][($objp->fk_parent_line?$objp->fk_parent_line:0)]['value_ppp']+=$objp->saldo*$objp->price;
					$this->aMovsal[$objp->rowid][($objp->fk_parent_line?$objp->fk_parent_line:0)]['value_peps']+=$objp->saldo*$objp->value_peps;
					$this->aMovsal[$objp->rowid][($objp->fk_parent_line?$objp->fk_parent_line:0)]['value_ueps']+=$objp->saldo*$objp->value_ueps;

					$this->aMovdet[$objp->rowid][$objp->fk]['datem']= $this->db->jdate($objp->datem);
					$this->aMovdet[$objp->rowid][$objp->fk]['type_mouvement']= 1;
					$this->aMovdet[$objp->rowid][$objp->fk]['qty']= $objp->saldo;
				}
				$i++;

			}

			//exit;
			$this->db->free($result);
			return $num;
		}
		return 0;
	}

	//metodo de valoracion
	function get_value_product($fk_entrepot,$date,$fk_product,$qty,$typemethod,$pmp,&$product)
	{
		global $conf,$langs,$user;

		require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementadd.class.php';
		$objMouvementadd = new Stockmouvementadd($this->db);

		if (empty($fk_entrepot) || empty($fk_product)) return -1;
		$this->aSales = array();
		//valuacion por el metodo peps
		$objMouvement = new MouvementStockext($this->db);
		$res = $objMouvement->method_valuation($fk_entrepot,$date,$fk_product);
		$aIng = $objMouvement->aIng;
		//recorremos los ingresos para realizar la salida correspondiente
		$qtysal = $qty;
		$qtyent = 0;
		if (empty($typemethod))
		{
			if (!$conf->global->STOCK_ALLOW_NEGATIVE_TRANSFER)
			{
				if($product->stock_warehouse[$fk_entrepot]->real < $qty)
				{
					$error=1;
					setEventMessages($langs->trans('No existe saldo suficiente'),null,'errors');
				}
				else
				{
					$this->aSales[0]['value'] = $pmp;
					$this->aSales[0]['qty'] = $qty;
				}
			}
			else
			{
				$this->aSales[0]['value'] = $pmp;
				$this->aSales[0]['qty'] = $qty;
			}
		}
		else
		{
			if(count($aIng)>0)
			{
				foreach ((array) $aIng AS $j => $lineing)
				{
					if ($lineing->balance_peps == 0)
						continue;
					elseif ($lineing->balance_peps > 0)
					{
						$value_peps = ($lineing->value_peps_adq?$lineing->value_peps_adq:$lineing->value_peps);
						if ($lineing->balance_peps >= $qtysal)
						{
							$aSaldo = array('value'=>$value_peps,'qty'=>$qtysal);
							$this->aSales[$lineing->id]['value'] = $value_peps;
							$this->aSales[$lineing->id]['qty'] = $qtysal;
							$qtyent += $qtysal;
							//actualizamos el saldo en stock_mouvement
							$resmadd = $objMouvementadd->fetch(0,$lineing->id);
							$this->aSales[$lineing->id]['id'] = $lineing->id;
							$objMouvementadd->balance_peps -= $qtysal;
							$resmadd = $objMouvementadd->update($user);
							if ($resmadd<=0)
							{
								$error=2;
								setEventMessages($objMouvementadd->error,$objMouvementadd->errors,'errors');
							}
							else
							{
								return 1;
							}
						}
						else
						{
							//$aSaldo = array('value'=>$lineing->value_peps,'qty'=>$lineing->balance_peps);
							$this->aSales[$lineing->id]['value'] = $value_peps;
							$this->aSales[$lineing->id]['qty'] = $lineing->balance_peps;
							$qtysal-=$lineing->balance_peps;
							$qtyent+=$lineing->balance_peps;

								//actualizamos el saldo en stock_mouvement
							$resmadd = $objMouvementadd->fetch(0,$lineing->id);
							$this->aSales[$lineing->id]['id'] = $lineing->id;
							$objMouvementadd->balance_peps -= $lineing->balance_peps;
							$resmadd = $objMouvementadd->update($user);
							if ($resmadd<=0)
							{
								$error=3;
								setEventMessages($objMouvementadd->error,$objMouvementadd->errors,'errors');
							}
						}
						//$this->aSales[$lineing->id] = $aSaldo;
					}
					else
					{
						$error=4;
						setEventMessages($langs->trans('No existe saldo suficiente para entregar').' '.$product->ref.' '.$product->label,null,'errors');
					}
				}
				if ($qty != $qtyent)
				{
					$error=5;
					if ($qty > $qtyent)
					{
						setEventMessages($langs->trans('NO existe saldo en almacen para cubrir la salida de').' '.$product->ref.' '.$product->label,null,'errors');
					}
					else
					{
						setEventMessages($langs->trans('Se esta entregando en demasia').' '.$product->ref.' '.$product->label,null,'errors');
					}
				}
			}
			else
			{
				if (!$conf->global->STOCK_ALLOW_NEGATIVE_TRANSFER)
				{
					$error=6;
					setEventMessages($langs->trans('No existe saldo suficiente'),null,'warnings');
				}
			}
		}
		//echo '<hr>validando';
		//	echo '<pre>';
		//	print_r($this->aMoving);
		//	print_r($this->aMovsal);
		//	echo '</pre>';
		//exit;
		if (!$error) return 1;
		else
		{
			return $error*-1;
		}
	}

	//function para recuerar el saldo y su valor segun metodo peps
	function get_balance_peps($entrepotid=0,$fk_product=0,$idsproduct='')
	{
		global $conf,$user;
		$sql = "SELECT m.rowid AS fk, p.rowid, p.ref as product_ref, p.label as produit, p.fk_product_type as type,";
		$sql.= " m.rowid as mid, m.value AS saldo, m.datem, m.fk_user_author, m.label, m.fk_origin, m.origintype,m.datem, m.type_mouvement, m.price, m.fk_entrepot ";
		//$sql.= " u.login ";
		$sql.= " , ma.value_peps, ma.value_ueps, ma.fk_parent_line, ma.balance_peps, ma.balance_ueps, ma.qty AS qtyadd ";
		$sql.= " FROM (".MAIN_DB_PREFIX."stock_mouvement as m)";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product as p ON m.fk_product = p.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."stock_mouvement_add AS ma ON ma.fk_stock_mouvement = m.rowid";
		//$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."user as u ON m.fk_user_author = u.rowid";
		$sql.= " WHERE 1 ";
		if (empty($conf->global->STOCK_SUPPORTS_SERVICES)) $sql.= " AND p.fk_product_type = 0";
		if ($entrepotid>0)
		{
			$sql.= " AND m.fk_entrepot ='".$entrepotid."'";
		}
		if ($fk_product>0)
		{
			$sql.= " AND p.rowid =".$fk_product;
		}
		elseif ($idsproduct)
		{
			$sql.= " AND p.rowid IN( ".$idsproduct.")";
		}
		$sql.= " AND ma.balance_peps > 0";

		//$sql.= " AND m.datem BETWEEN '".$this->db->idate($fechaIni)."' AND '".$this->db->idate($fechaFin)."'";

		//if ($conf->global->ALMACEN_FILTER_YEAR)
		//	$sql .= " AND year(m.datem) = ".$year;
		$sql.= " ORDER BY m.datem ASC, m.rowid ASC ";

		$resql = $this->db->query($sql);
		$this->aBalance = array();
		if ($resql)
		{
			$i = 0;
			$num = $this->db->num_rows($resql);
			while ($i < $num)
			{
				$objp = $this->db->fetch_object($resql);
				//actualizando totales
					//$this->aMov[$objp->rowid]['qty']+$objp->saldo;

				if ($objp->type_mouvement == 3 || ($objp->type_mouvement == 0 && $objp->saldo >0))
				{
					if ($objp->value_peps>0)$this->actualPricepeps[$objp->rowid] = $objp->value_peps;
					if ($objp->price>0)$this->actualPriceppp[$objp->rowid] = $objp->price;

					$this->aBalance[$objp->rowid]['balance']+= $objp->balance_peps;
					$this->aBalance[$objp->rowid]['total']+= $objp->balance_peps*$objp->value_peps;
				}

				if ($objp->type_mouvement == 1 || $objp->type_mouvement == 2 || ($objp->type_mouvement == 0 && $objp->saldo < 0) )
				{
				}
				$i++;
			}

			$this->db->free($result);
			return $num;
		}
		return 0;
	}
}

/**
 * Class UsersessionLine
 */
class MouvementstockLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $datem;
	public $tms;
	public $fk_product;
	public $fk_entrepot;
	public $value;
	public $price;
	public $type_mouvement;
	public $fk_user_author;
	public $label;
	public $fk_origin;
	public $origintype;
	public $inventorycode;
	public $batch;
	public $eatby;
	public $sellby;

	/**
	 * @var mixed Sample line property 2
	 */

}

?>