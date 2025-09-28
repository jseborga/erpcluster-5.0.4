<?php
require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementtemp.class.php';

class StockMouvementtempext extends Stockmouvementtemp
{


	/**
	 * Load object in memory from the database
	 *
	 * @param string $sortorder Sort Order
	 * @param string $sortfield Sort field
	 * @param int    $limit     offset limit
	 * @param int    $offset    offset limit
	 * @param array  $filter    filter array
	 * @param string $filtermode filter mode (AND or OR)
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function fetchAlladd($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.entity,";
		$sql .= " t.ref,";
		$sql .= " t.tms,";
		$sql .= " t.datem,";
		$sql .= " t.fk_product,";
		$sql .= " t.fk_entrepot,";
		$sql .= " t.fk_type_mov,";
		$sql .= " t.value,";
		$sql .= " t.quant,";
		$sql .= " t.price,";
		$sql .= " t.balance_peps,";
		$sql .= " t.balance_ueps,";
		$sql .= " t.price_peps,";
		$sql .= " t.price_ueps,";
		$sql .= " t.type_mouvement,";
		$sql .= " t.fk_user_author,";
		$sql .= " t.label,";
		$sql .= " t.fk_origin,";
		$sql .= " t.origintype,";
		$sql .= " t.inventorycode,";
		$sql .= " t.batch,";
		$sql .= " t.eatby,";
		$sql .= " t.sellby,";
		$sql .= " t.statut";
		$sql.= " , d.fk_entrepot_from, d.fk_entrepot_to, d.rowid AS rowiddoc ";
		$sql.= " , c.type AS typemov ";

		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."stock_mouvement_doc AS d ON t.ref = d.ref AND t.entity = d.entity";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."c_type_mouvement AS c ON t.fk_type_mov = c.rowid ";
		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("stockprogram", 1) . ")";
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND ' . implode(' '.$filtermode.' ', $sqlwhere);
		}
		if ($filterstatic){
			$sql.= $filterstatic;
		}
		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield,$sortorder);
		}
		if (!empty($limit)) {
		 $sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}

		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new StockmouvementtempLine();

				$line->id = $obj->rowid;

				$line->entity = $obj->entity;
				$line->ref = $obj->ref;
				$line->tms = $this->db->jdate($obj->tms);
				$line->datem = $this->db->jdate($obj->datem);
				$line->fk_product = $obj->fk_product;
				$line->fk_entrepot = $obj->fk_entrepot;
				$line->fk_type_mov = $obj->fk_type_mov;
				$line->value = $obj->value;
				$line->quant = $obj->quant;
				$line->price = $obj->price;
				$line->balance_peps = $obj->balance_peps;
				$line->balance_ueps = $obj->balance_ueps;
				$line->price_peps = $obj->price_peps;
				$line->price_ueps = $obj->price_ueps;
				$line->type_mouvement = $obj->type_mouvement;
				$line->fk_user_author = $obj->fk_user_author;
				$line->label = $obj->label;
				$line->fk_origin = $obj->fk_origin;
				$line->origintype = $obj->origintype;
				$line->inventorycode = $obj->inventorycode;
				$line->batch = $obj->batch;
				$line->eatby = $this->db->jdate($obj->eatby);
				$line->sellby = $this->db->jdate($obj->sellby);
				$line->statut = $obj->statut;
				$line->fk_entrepot_from = $obj->fk_entrepot_from;
				$line->fk_entrepot_to = $obj->fk_entrepot_to;
				$line->rowiddoc = $obj->rowiddoc;
				$line->typemov = $obj->typemov;

				if ($lView && $num == 1) $this->fetch($obj->rowid);

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

	public function updateline()
	{
		global $conf;
		if (empty($this->value)) $this->value = 0;

		$sql = "UPDATE ".MAIN_DB_PREFIX.$this->table_element." SET fk_product = ".$this->fk_product;
		$sql.= " , value = ".$this->value;
		$sql.= " , balance_peps = ".$this->balance_peps;
		$sql.= " , balance_ueps = ".$this->balance_ueps;
		$sql.= " , price = ".$this->price;
		$sql.= " , price_peps = ".$this->price_peps;
		$sql.= " , price_ueps = ".$this->price_ueps;

		$sql.= " WHERE rowid = ".$this->id;
		dol_syslog(get_class($this)."::_updateline update stock value", LOG_DEBUG);
		$this->db->begin();
		$resql=$this->db->query($sql);
		if (! $resql)
		{
			$this->db->rollback();
			$this->errors[]=$this->db->lasterror();
			$error = -1;
			return $error;
		}
		else
		{
			$this->db->commit();
			$error=0;
			return 1;
		}
	}

		/**
	*	Add a movement of stock (in one direction only)
	*
	*	@param		User	$user			User object
	*	@param		int		$fk_product		Id of product
	*	@param		int		$entrepot_id	Id of warehouse
	*	@param		int		$qty			Qty of movement (can be <0 or >0 depending on parameter type)
	*	@param		int		$type			Direction of movement:
	*										0=input (stock increase after stock transfert), 1=output (stock decrease after stock transfer),
	*										2=output (stock decrease), 3=input (stock increase)
	*                                      Note that qty should be > 0 with 0 or 3, < 0 with 1 or 2.
	*	@param		int		$price			Unit price HT of product, used to calculate average weighted price (PMP in french). If 0, average weighted price is not changed.
	*	@param		string	$label			Label of stock movement
	*	@param		string	$inventorycode	Inventory code
	*	@param		string	$datem			Force date of movement
	*	@param		date	$eatby			eat-by date
	*	@param		date	$sellby			sell-by date
	*	@param		string	$batch			batch number
	*	@param		boolean	$skip_batch		If set to true, stock movement is done without impacting batch record
	*	@return		int						<0 if KO, 0 if fk_product is null, >0 if OK
	*/
	function _create($user, $fk_product, $entrepot_id, $ref, $qty, $type, $price=0, $label='', $inventorycode='', $datem='',$eatby='',$sellby='',$batch='',$skip_batch=false, $fk_type_mov=0,$balance_peps=0,$balance_ueps=0,$price_peps=0,$price_ueps=0)
	{
		global $conf, $langs;
		require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
		$error = 0;
		dol_syslog(get_class($this)."::_create start userid=$user->id, fk_product=$fk_product, warehouse=$entrepot_id, qty=$qty, type=$type, price=$price, label=$label, inventorycode=$inventorycode, datem=".$datem.", eatby=".$eatby.", sellby=".$sellby.", batch=".$batch.", skip_batch=".$skip_batch);
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
			$this->errors[]='ErrorBadValueForParameterEatBy';
			return -1;
		}
		// Set properties of movement
		$this->entity = $conf->entity;
		$this->ref = $ref;
		$this->product_id = $fk_product;
		$this->entrepot_id = $entrepot_id;
		$this->qty = $qty;
		$this->type = $type;
		$this->fk_type_mov = $fk_type_mov;
		$this->balance_peps = $balance_peps;
		$this->balance_ueps = $balance_ueps;
		$this->price_peps = $price_peps;
		$this->price_ueps = $price_ueps;
		$this->statut = 1; //validado
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
			//if (empty($batch) && empty($eatby) && empty($sellby))
			if (empty($batch))
			{
				$this->errors[]=$langs->trans("ErrorTryToMakeMoveOnProductRequiringBatchData", $product->name);
				dol_syslog("Try to make a movement of a product with status_batch on without any batch data");

				$this->db->rollback();
				return -2;
			}

			// If a serial number is provided, we check that sellby and eatby match already existing serial
			$sql = "SELECT pb.rowid, pb.batch, pb.eatby, pb.sellby FROM ".MAIN_DB_PREFIX."product_batch as pb, ".MAIN_DB_PREFIX."product_stock as ps";
			$sql.= " WHERE pb.fk_product_stock = ps.rowid AND ps.fk_product = ".$fk_product." AND pb.batch = '".$this->db->escape($batch)."'";
			dol_syslog(get_class($this)."::_create scan serial for this product to check if eatby and sellby match", LOG_DEBUG);
			$resql = $this->db->query($sql);
			if ($resql)
			{
				$num = $this->db->num_rows($resql);
				$i=0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					if ($this->db->jdate($obj->eatby) != $eatby)
					{
						$this->errors[]=$langs->trans("ThisSerialAlreadyExistWithDifferentDate", $batch, $this->db->jdate($obj->eatby), $eatby);
						dol_syslog($langs->trans("ThisSerialAlreadyExistWithDifferentDate", $batch, $this->db->jdate($obj->eatby), $eatby));
						$this->db->rollback();
						return -3;
					}
					if ($this->db->jdate($obj->sellby) != $sellby)
					{
						$this->errors[]=$langs->trans("ThisSerialAlreadyExistWithDifferentDate", $batch, $this->db->jdate($obj->sellby), $sellby);
						dol_syslog($langs->trans("ThisSerialAlreadyExistWithDifferentDate", $batch, $this->db->jdate($obj->sellby), $sellby));
						$this->db->rollback();
						return -3;
					}
					$i++;
				}
			}
			else
			{
				dol_print_error($this->db);
				$this->db->rollback();
				return -1;
			}
		}
		// TODO Check qty is ok for stock move.
		if (! empty($conf->productbatch->enabled) && $product->hasbatch() && ! $skip_batch)
		{

		}
		else
		{

		}

		// Define if we must make the stock change (If product type is a service or if stock is used also for services)
		$movestock=0;
		// if ($product->type != Product::TYPE_SERVICE || ! empty($conf->global->STOCK_SUPPORTS_SERVICES)) $movestock=1;
		if ($product->type != 1 || ! empty($conf->global->STOCK_SUPPORTS_SERVICES)) $movestock=1;

		if ($movestock && $entrepot_id > 0)	// Change stock for current product, change for subproduct is done after
		{
			if(!empty($this->origin))
			{			// This is set by caller for tracking reason
				$origintype = $this->origin->element;
				$fk_origin = $this->origin->id;
			}
			else
			{
				$origintype = '';
				$fk_origin = 0;
			}
			$sql = "INSERT INTO ".MAIN_DB_PREFIX."stock_mouvement_temp(";
			$sql.= " datem, fk_product, entity, ref, batch, eatby, sellby,";
			$sql.= " fk_entrepot, fk_type_mov, value, type_mouvement, fk_user_author, label, inventorycode, price, fk_origin, origintype, statut, balance_peps, balance_ueps, price_peps, price_ueps ";
			$sql.= ")";
			$sql.= " VALUES ('".$this->db->idate($now)."', ".$this->product_id.", ";
			$sql.= $this->entity.", ";
			$sql.= " '".$this->ref."', ";
			$sql.= " ".($batch?"'".$batch."'":"null").", ";
			$sql.= " ".($eatby?"'".$this->db->idate($eatby)."'":"null").", ";
			$sql.= " ".($sellby?"'".$this->db->idate($sellby)."'":"null").", ";
			$sql.= " ".$this->entrepot_id.", ".$this->fk_type_mov.", ".$this->qty.", ".$this->type.",";
			$sql.= " ".$user->id.",";
			$sql.= " '".$this->db->escape($label)."',";
			$sql.= " ".($inventorycode?"'".$this->db->escape($inventorycode)."'":"null").",";
			$sql.= " '".price2num($price)."',";
			$sql.= " '".$fk_origin."',";
			$sql.= " '".$origintype."',";
			$sql.= " ".$this->statut;
			$sql.= ", ".$this->balance_peps;
			$sql.= ", ".$this->balance_ueps;
			$sql.= ", ".$this->price_peps;
			$sql.= ", ".$this->price_ueps;
			$sql.= ")";
			//echo '<hr>'.$sql;
			dol_syslog(get_class($this)."::_create", LOG_DEBUG);
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
			//$oldpmpwarehouse=0;

			// Test if there is already a record for couple (warehouse / product)
			$num = 0;
		}

		// Add movement for sub products (recursive call)
		if (! $error && ! empty($conf->global->PRODUIT_SOUSPRODUITS) && empty($conf->global->INDEPENDANT_SUBPRODUCT_STOCK))
		{
			$error = $this->_createSubProduct($user, $fk_product, $entrepot_id, $qty, $type, 0, $label, $inventorycode);	// we use 0 as price, because pmp is not changed for subproduct
		}

		if ($movestock && ! $error)
		{
		  // Call trigger
		  // $result=$this->call_trigger('STOCK_MOVEMENT',$user);
		  // if ($result < 0) $error++;
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
			dol_syslog(get_class($this)."::_create error code=".$error, LOG_ERR);
			return -6;
		}
	}

	/**
	 *  Returns the reference to the following non used Order depending on the active numbering module
	 *  defined into ALMACEN_ADDON
	 *
	 *  @param	Societe		$soc  	Object thirdparty
	 *  @return string      		Order free reference
	 */
	function getNextNumRef($soc)
	{
		global $db, $langs, $conf;
		$langs->load("almacen@almacen");

		$dir = DOL_DOCUMENT_ROOT . "/almacen/core/modules";

	  // if (! empty($conf->global->ALMACEN_ADDON))
	  //   {
		$file = "mod_almacen_ubuntubo_transf.php";
	  // Chargement de la classe de numerotation
		$classname = "mod_almacen_ubuntubo_transf";
		$result=include_once $dir.'/'.$file;
		if ($result)
		{
			$obj = new $classname();
			$numref = "";
			$numref = $obj->getNextValue($soc,$this);

			if ( $numref != "")
			{
				return $numref;
			}
			else
			{
				dol_print_error($db,"Stockmouvementtemp::getNextNumRef ".$obj->error);
				return "";
			}
		}
		else
		{
			print $langs->trans("Error")." ".$langs->trans("Error_ALMACEN_ADDON_NotDefined");
			return "";
		}
	}

	/**
	 *	Return statut label of Order
	 *
	 *	@param      int		$mode       0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *	@return     string      		Libelle
	 */
	function getLibStatutx($mode)
	{
		return $this->LibStatut($this->statut,$this->facturee,$mode);
	}

	/**
	 *	Return label of statut
	 *
	 *	@param		int		$statut      	Id statut
	 *  @param      int		$facturee    	if invoiced
	 *	@param      int		$mode        	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return     string					Label of statut
	 */
	function LibStatutx($statut,$facturee,$mode)
	{
		global $langs;
		//print 'x'.$statut.'-'.$facturee;
		if ($mode == 0)
		{
			if ($statut==-1) return $langs->trans('StatusTransfCanceled');
			if ($statut==0) return $langs->trans('StatusTransfDraft');
			if ($statut==1) return $langs->trans('StatusTransfPending');
			if ($statut==2) return $langs->trans('StatusTransfAccepted');
		}
		elseif ($mode == 1)
		{
			if ($statut==-1) return $langs->trans('StatusOrderCanceledShort');
			if ($statut==0) return $langs->trans('StatusOrderDraftShort');
			if ($statut==1) return $langs->trans('StatusOrderPendingShort');
			if ($statut==2) return $langs->trans('StatusOrderSentShort');
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlist($ref,$statut='',$filter="",$nameorder='ref',$order='DESC')
	{
		global $langs,$conf;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.ref,";
		$sql.= " t.tms,";
		$sql.= " t.datem,";
		$sql.= " t.fk_product,";
		$sql.= " t.fk_entrepot,";
		$sql.= " t.fk_type_mov,";
		$sql.= " t.value,";
		$sql.= " t.quant,";
		$sql.= " t.price,";
		$sql.= " t.type_mouvement,";
		$sql.= " t.fk_user_author,";
		$sql.= " t.label,";
		$sql.= " t.fk_origin,";
		$sql.= " t.origintype,";
		$sql.= " t.inventorycode,";
		$sql.= " t.batch,";
		$sql.= " t.eatby,";
		$sql.= " t.sellby,";
		$sql.= " t.statut";


		$sql.= " FROM ".MAIN_DB_PREFIX."stock_mouvement_temp as t";
		$sql.= " WHERE t.entity = ".$conf->entity;
		if ($ref)
		{
			$sql.= " AND t.ref = '".$ref."'";
		}
		if ($filter)
			$sql.= " AND ".$filter;
		   //$sql.= " AND t.fk_entrepot = ".$fk_entrepot;
		if ($statut)
			$sql.= " AND t.statut IN (".$statut.")";
		//order
		if (!empty($nameorder))
			$sql.= " ORDER BY ".$nameorder.' '.(!empty($order)?$order:'');
		dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();

		$num = 0;
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$num = $this->db->num_rows($resql);
				$i  = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Stockmouvementtemp($this->db);

					$objnew->id    = $obj->rowid;

					$objnew->entity = $obj->entity;
					$objnew->ref = $obj->ref;
					$objnew->tms = $this->db->jdate($obj->tms);
					$objnew->datem = $this->db->jdate($obj->datem);
					$objnew->fk_product = $obj->fk_product;
					$objnew->fk_entrepot = $obj->fk_entrepot;
					$objnew->fk_type_mov = $obj->fk_type_mov;
					$objnew->value = $obj->value;
					$objnew->quant = $obj->quant;
					$objnew->price = $obj->price;
					$objnew->type_mouvement = $obj->type_mouvement;
					$objnew->fk_user_author = $obj->fk_user_author;
					$objnew->label = $obj->label;
					$objnew->fk_origin = $obj->fk_origin;
					$objnew->origintype = $obj->origintype;
					$objnew->inventorycode = $obj->inventorycode;
					$objnew->batch = $obj->batch;
					$objnew->eatby = $this->db->jdate($obj->eatby);
					$objnew->sellby = $this->db->jdate($obj->sellby);
					$objnew->statut = $obj->statut;
					$this->array[$obj->rowid] = $objnew;
					$i++;
				}
			}
			$this->db->free($resql);
			return $num;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Create movement in database for all subproducts
	 *
	 * 	@param 		User	$user			Object user
	 * 	@param		int		$idProduct		Id product
	 * 	@param		int		$entrepot_id	Warehouse id
	 * 	@param		int		$qty			Quantity
	 * 	@param		int		$type			Type
	 * 	@param		int		$price			Price
	 * 	@param		string	$label			Label of movement
	 *  @param		string	$inventorycode	Inventory code
	 * 	@return 	int     				<0 if KO, 0 if OK
	 */
	function _createSubProduct($user, $idProduct, $entrepot_id, $qty, $type, $price=0, $label='', $inventorycode='')
	{
		$error = 0;
		$pids = array();
		$pqtys = array();
		$sql = "SELECT fk_product_pere, fk_product_fils, qty";
		$sql.= " FROM ".MAIN_DB_PREFIX."product_association";
		$sql.= " WHERE fk_product_pere = ".$idProduct;
		$sql.= " AND incdec = 1";

		dol_syslog(get_class($this)."::_createSubProduct", LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$i=0;
			while ($obj=$this->db->fetch_object($resql))
			{
				$pids[$i]=$obj->fk_product_fils;
				$pqtys[$i]=$obj->qty;
				$i++;
			}
			$this->db->free($resql);
		}
		else
		{
			$error = -2;
		}

		// Create movement for each subproduct
		foreach($pids as $key => $value)
		{
			$tmpmove = clone $this;
			$tmpmove->_create($user, $pids[$key], $entrepot_id, ($qty * $pqtys[$key]), $type, 0, $label, $inventorycode);		// This will also call _createSubProduct making this recursive
			unset($tmpmove);
		}

		return $error;
	}
}
?>