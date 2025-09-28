<?php
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';

class Factureext extends Facture
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND', $filterstatic='', $lView=false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT f.rowid,f.facnumber,f.ref_client,f.ref_ext,f.ref_int,f.type,f.fk_soc,f.amount';
		$sql.= ', f.tva, f.localtax1, f.localtax2, f.total, f.total_ttc, f.revenuestamp';
		$sql.= ', f.remise_percent, f.remise_absolue, f.remise';
		$sql.= ', f.datef as df, f.date_pointoftax';
		$sql.= ', f.date_lim_reglement as dlr';
		$sql.= ', f.datec as datec';
		$sql.= ', f.date_valid as datev';
		$sql.= ', f.tms as datem';
		$sql.= ', f.note_private, f.note_public, f.fk_statut, f.paye, f.close_code, f.close_note, f.fk_user_author, f.fk_user_valid, f.model_pdf';
		$sql.= ', f.fk_facture_source';
		$sql.= ', f.fk_mode_reglement, f.fk_cond_reglement, f.fk_projet, f.extraparams';
		$sql.= ', f.situation_cycle_ref, f.situation_counter, f.situation_final';
		$sql.= ', f.fk_account';
		$sql.= ", f.fk_multicurrency, f.multicurrency_code, f.multicurrency_tx, f.multicurrency_total_ht, f.multicurrency_total_tva, f.multicurrency_total_ttc";
		$sql.= ', p.code as mode_reglement_code, p.libelle as mode_reglement_libelle';
		$sql.= ', c.code as cond_reglement_code, c.libelle as cond_reglement_libelle, c.libelle_facture as cond_reglement_libelle_doc';
        $sql.= ', f.fk_incoterms, f.location_incoterms';
        $sql.= ", i.libelle as libelle_incoterms";
        $sql.= " , v.nfiscal ";
        $sql.= " , vd.num_autoriz, vd.activity ";
		$sql.= ' FROM '.MAIN_DB_PREFIX.'facture as f';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_payment_term as c ON f.fk_cond_reglement = c.rowid';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_paiement as p ON f.fk_mode_reglement = p.id';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_incoterms as i ON f.fk_incoterms = i.rowid';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'v_fiscal as v ON v.fk_facture = f.rowid';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'v_dosing as vd ON v.fk_dosing = vd.rowid';

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND f.entity IN (" . getEntity("conc", 1) . ")";
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND ' . implode(' '.$filtermode.' ', $sqlwhere);
		}
		if ($filterstatic) $sql.= $filterstatic;

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield,$sortorder);
		}
		if (!empty($limit)) {
		 $sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}

		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql))
			{
				$line = new Factureext($this->db);

				$line->id					= $obj->rowid;
				$line->ref					= $obj->facnumber;
				$line->ref_client			= $obj->ref_client;
				$line->ref_ext				= $obj->ref_ext;
				$line->ref_int				= $obj->ref_int;
				$line->type					= $obj->type;
				$line->date					= $this->db->jdate($obj->df);
				$line->date_pointoftax		= $this->db->jdate($obj->date_pointoftax);
				$line->date_creation		= $this->db->jdate($obj->datec);
				$line->date_validation		= $this->db->jdate($obj->datev);
				$line->datem				= $this->db->jdate($obj->datem);
				$line->remise_percent		= $obj->remise_percent;
				$line->remise_absolue		= $obj->remise_absolue;
				$line->total_ht				= $obj->total;
				$line->total_tva			= $obj->tva;
				$line->total_localtax1		= $obj->localtax1;
				$line->total_localtax2		= $obj->localtax2;
				$line->total_ttc			= $obj->total_ttc;
				$line->revenuestamp         = $obj->revenuestamp;
				$line->paye					= $obj->paye;
				$line->close_code			= $obj->close_code;
				$line->close_note			= $obj->close_note;
				$line->socid				= $obj->fk_soc;
				$line->statut				= $obj->fk_statut;
				$line->date_lim_reglement	= $this->db->jdate($obj->dlr);
				$line->mode_reglement_id	= $obj->fk_mode_reglement;
				$line->mode_reglement_code	= $obj->mode_reglement_code;
				$line->mode_reglement		= $obj->mode_reglement_libelle;
				$line->cond_reglement_id	= $obj->fk_cond_reglement;
				$line->cond_reglement_code	= $obj->cond_reglement_code;
				$line->cond_reglement		= $obj->cond_reglement_libelle;
				$line->cond_reglement_doc	= $obj->cond_reglement_libelle_doc;
				$line->fk_account           = ($obj->fk_account>0)?$obj->fk_account:null;
				$line->fk_project			= $obj->fk_projet;
				$line->fk_facture_source	= $obj->fk_facture_source;
				$line->note					= $obj->note_private;	// deprecated
				$line->note_private			= $obj->note_private;
				$line->note_public			= $obj->note_public;
				$line->user_author			= $obj->fk_user_author;
				$line->user_valid			= $obj->fk_user_valid;
				$line->modelpdf				= $obj->model_pdf;
				$line->situation_cycle_ref  = $obj->situation_cycle_ref;
				$line->situation_counter    = $obj->situation_counter;
				$line->situation_final      = $obj->situation_final;
				$line->extraparams			= (array) json_decode($obj->extraparams, true);

				//Incoterms
				$line->fk_incoterms = $obj->fk_incoterms;
				$line->location_incoterms = $obj->location_incoterms;
				$line->libelle_incoterms = $obj->libelle_incoterms;

				// Multicurrency
				$line->fk_multicurrency 		= $obj->fk_multicurrency;
				$line->multicurrency_code 		= $obj->multicurrency_code;
				$line->multicurrency_tx 		= $obj->multicurrency_tx;
				$line->multicurrency_total_ht 	= $obj->multicurrency_total_ht;
				$line->multicurrency_total_tva 	= $obj->multicurrency_total_tva;
				$line->multicurrency_total_ttc 	= $obj->multicurrency_total_ttc;

				//v_fiscal
				$line->num_autoriz = $obj->num_autoriz;
				$line->activity = $obj->activity;
				$line->nfiscal = $obj->nfiscal;

				if ($line->type == self::TYPE_SITUATION && $fetch_situation)
				{
					$this->fetchPreviousNextSituationInvoice();
				}

				if ($line->statut == self::STATUS_DRAFT)	$line->brouillon = 1;

				if ($lView && $num == 1) $this->fetch($obj->rowid);

				// Retrieve all extrafield for invoice
				// fetch optionals attributes and labels
				require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
				$extrafields=new ExtraFields($this->db);
				$extralabels=$extrafields->fetch_name_optionals_label($this->table_element,true);
				$this->fetch_optionals($line->id,$extralabels);

				/*
				 * Lines
				*/

				$line->lines  = array();

				$result=$line->fetch_lines();
				if ($result < 0)
				{
					$this->error=$this->db->error();
					return -3;
				}
				$this->lines[$obj->rowid] = $line;
			}
			return $num;
		}
		else
		{
			$this->error=$this->db->error();
			return -1;
		}
	}


	/**
	 *	Delete invoice
	 *
	 *	@param     	User	$user      	    User making the deletion.
	 *	@param		int		$notrigger		1=Does not execute triggers, 0= execute triggers
	 *	@param		int		$idwarehouse	Id warehouse to use for stock change.
	 *	@return		int						<0 if KO, >0 if OK
	 */
	function deleteadd($user, $notrigger=0, $idwarehouse=-1,$coste=false)
	{
		global $langs,$conf;
		require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

		if (empty($rowid)) $rowid=$this->id;

		dol_syslog(get_class($this)."::delete rowid=".$rowid, LOG_DEBUG);

		// TODO Test if there is at least one payment. If yes, refuse to delete.

		$error=0;
		$this->db->begin();

		if (! $error && ! $notrigger)
		{
            // Call trigger
			$result=$this->call_trigger('BILL_DELETE',$user);
			if ($result < 0) $error++;
            // End call triggers
		}

		// Removed extrafields
		if (! $error) {
			$result=$this->deleteExtraFields();
			if ($result < 0)
			{
				$error++;
				dol_syslog(get_class($this)."::delete error deleteExtraFields ".$this->error, LOG_ERR);
			}
		}

		if (! $error)
		{
			// Delete linked object
			$res = $this->deleteObjectLinked();
			if ($res < 0) $error++;
		}

		if (! $error)
		{
			// If invoice was converted into a discount not yet consumed, we remove discount
			$sql = 'DELETE FROM '.MAIN_DB_PREFIX.'societe_remise_except';
			$sql.= ' WHERE fk_facture_source = '.$rowid;
			$sql.= ' AND fk_facture_line IS NULL';
			$resql=$this->db->query($sql);

			// If invoice has consumned discounts
			$this->fetch_lines();
			$list_rowid_det=array();
			foreach($this->lines as $key => $invoiceline)
			{
				$list_rowid_det[]=$invoiceline->rowid;
			}

			// Consumned discounts are freed
			if (count($list_rowid_det))
			{
				$sql = 'UPDATE '.MAIN_DB_PREFIX.'societe_remise_except';
				$sql.= ' SET fk_facture = NULL, fk_facture_line = NULL';
				$sql.= ' WHERE fk_facture_line IN ('.join(',',$list_rowid_det).')';

				dol_syslog(get_class($this)."::delete", LOG_DEBUG);
				if (! $this->db->query($sql))
				{
					$this->error=$this->db->error()." sql=".$sql;
					$this->db->rollback();
					return -5;
				}
			}

			// If we decrement stock on invoice validation, we increment
			if ($this->type != self::TYPE_DEPOSIT && $result >= 0 && ! empty($conf->stock->enabled) && ! empty($conf->global->STOCK_CALCULATE_ON_BILL) && $idwarehouse!=-1)
				{
					require_once DOL_DOCUMENT_ROOT.'/product/stock/class/mouvementstock.class.php';
					require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementaddext.class.php';
					$langs->load("agenda");

					$num=count($this->lines);
					for ($i = 0; $i < $num; $i++)
					{
						if ($this->lines[$i]->fk_product > 0)
						{
							$mouvP = new MouvementStock($this->db);
							$mouvPtmp = new MouvementStock($this->db);
							$mouvP->origin = &$this;
							$mouvPAdd = new Stockmouvementaddext($this->db);
							$price = 0;
							if ($coste){
							//vamos a buscar el precio al cual salio el producto
								$filter = " AND t.fk_facturedet = ".$this->lines[$i]->id;
								$resadd = $mouvPadd->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
								if ($resadd==1)
								{
									if ($conf->global->ALMACEN_METHOD_VALUATION_INVENTORY==0)
									{
									//VALUACION PPP
										$mouvPtmp->fetch($mouvPadd->fk_stock_mouvement);
										$price = $mouvPtmp->price;
									}
									elseif($conf->global->ALMACEN_METHOD_VALUATION_INVENTORY==1)
									{
									//VALUATION PEPS
										$price = $mouvPadd->value_peps;
									}
								}
							}
						// We decrease stock for product
							if ($this->type == self::TYPE_CREDIT_NOTE) $result=$mouvP->livraison($user, $this->lines[$i]->fk_product, $idwarehouse, $this->lines[$i]->qty, $this->lines[$i]->subprice, $langs->trans("InvoiceDeleteDolibarr",$this->ref));
						else $result=$mouvP->reception($user, $this->lines[$i]->fk_product, $idwarehouse, $this->lines[$i]->qty, $price, $langs->trans("InvoiceDeleteDolibarr",$this->ref));	// we use 0 for price, to not change the weighted average value
					}
				}
			}

			// Delete invoice line
			$sql = 'DELETE FROM '.MAIN_DB_PREFIX.'facturedet WHERE fk_facture = '.$rowid;

			dol_syslog(get_class($this)."::delete", LOG_DEBUG);

			if ($this->db->query($sql) && $this->delete_linked_contact())
			{
				$sql = 'DELETE FROM '.MAIN_DB_PREFIX.'facture WHERE rowid = '.$rowid;

				dol_syslog(get_class($this)."::delete", LOG_DEBUG);

				$resql=$this->db->query($sql);
				if ($resql)
				{
					// On efface le repertoire de pdf provisoire
					$ref = dol_sanitizeFileName($this->ref);
					if ($conf->facture->dir_output && !empty($this->ref))
					{
						$dir = $conf->facture->dir_output . "/" . $ref;
						$file = $conf->facture->dir_output . "/" . $ref . "/" . $ref . ".pdf";
						if (file_exists($file))	// We must delete all files before deleting directory
						{
							$ret=dol_delete_preview($this);

							if (! dol_delete_file($file,0,0,0,$this)) // For triggers
							{
								$this->error=$langs->trans("ErrorCanNotDeleteFile",$file);
								$this->db->rollback();
								return 0;
							}
						}
						if (file_exists($dir))
						{
							if (! dol_delete_dir_recursive($dir)) // For remove dir and meta
							{
								$this->error=$langs->trans("ErrorCanNotDeleteDir",$dir);
								$this->db->rollback();
								return 0;
							}
						}
					}

					$this->db->commit();
					return 1;
				}
				else
				{
					$this->error=$this->db->lasterror()." sql=".$sql;
					$this->db->rollback();
					return -6;
				}
			}
			else
			{
				$this->error=$this->db->lasterror()." sql=".$sql;
				$this->db->rollback();
				return -4;
			}
		}
		else
		{
			$this->db->rollback();
			return -2;
		}
	}
	/**
	 * Tag invoice as validated + call trigger BILL_VALIDATE
	 * Object must have lines loaded with fetch_lines
	 *
	 * @param	User	$user           Object user that validate
	 * @param   string	$force_number	Reference to force on invoice
	 * @param	int		$idwarehouse	Id of warehouse to use for stock decrease if option to decreasenon stock is on (0=no decrease)
	 * @param	int		$notrigger		1=Does not execute triggers, 0= execute triggers
     * @return	int						<0 if KO, >0 if OK
	 */
	function validateadd($user, $force_number='', $idwarehouse=0, $notrigger=0)
	{
		global $conf,$langs;
		require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

		$now=dol_now();

		$error=0;
		dol_syslog(get_class($this).'::validate user='.$user->id.', force_number='.$force_number.', idwarehouse='.$idwarehouse);

		// Check parameters
		if (! $this->brouillon)
		{
			dol_syslog(get_class($this)."::validate no draft status", LOG_WARNING);
			return 0;
		}

		if ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->facture->creer))
			|| (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->facture->invoice_advance->validate)))
		{
			$this->error='Permission denied';
			dol_syslog(get_class($this)."::validate ".$this->error.' MAIN_USE_ADVANCED_PERMS='.$conf->global->MAIN_USE_ADVANCED_PERMS, LOG_ERR);
			return -1;
		}

		$this->db->begin();

		$this->fetch_thirdparty();
		$this->fetch_lines();

		// Check parameters
		if (
			$this->type == self::TYPE_REPLACEMENT )

		{
			// si facture de remplacement
			// Controle que facture source connue
			if ($this->fk_facture_source <= 0)
			{
				$this->error=$langs->trans("ErrorFieldRequired",$langs->trans("InvoiceReplacement"));
				$this->db->rollback();
				return -10;
			}

				// Charge la facture source a remplacer
			$facreplaced=new Facture($this->db);
			$result=$facreplaced->fetch($this->fk_facture_source);
			if ($result <= 0)
			{
				$this->error=$langs->trans("ErrorBadInvoice");
				$this->db->rollback();
				return -11;
			}

				// Controle que facture source non deja remplacee par une autre
			$idreplacement=$facreplaced->getIdReplacingInvoice('validated');
			if ($idreplacement && $idreplacement != $this->id)
			{
				$facreplacement=new Facture($this->db);
				$facreplacement->fetch($idreplacement);
				$this->error=$langs->trans("ErrorInvoiceAlreadyReplaced",$facreplaced->ref,$facreplacement->ref);
				$this->db->rollback();
				return -12;
			}

			$result=$facreplaced->set_canceled($user,'replaced','');
			if ($result < 0)
			{
				$this->error=$facreplaced->error;
				$this->db->rollback();
				return -13;
			}
		}

			// Define new ref
		if ($force_number)
		{
			$num = $force_number;
		}
		else if (preg_match('/^[\(]?PROV/i', $this->ref) || empty($this->ref))
			// empty should not happened, but when it occurs, the test save life
		{
			if (! empty($conf->global->FAC_FORCE_DATE_VALIDATION))
			// If option enabled, we force invoice date
			{
				$this->date=dol_now();
				$this->date_lim_reglement=$this->calculate_date_lim_reglement();
			}
			$num = $this->getNextNumRef($this->thirdparty);
		}
		else
		{
			$num = $this->ref;
		}
		$this->newref = $num;

		if ($num)
		{
			$this->update_price(1);

			// Validate
			$sql = 'UPDATE '.MAIN_DB_PREFIX.'facture';
			$sql.= " SET facnumber='".$num."', fk_statut = ".self::STATUS_VALIDATED.", fk_user_valid = ".$user->id.", date_valid = '".$this->db->idate($now)."'";
			if (! empty($conf->global->FAC_FORCE_DATE_VALIDATION))
			// If option enabled, we force invoice date
			{
				$sql.= ", datef='".$this->db->idate($this->date)."'";
				$sql.= ", date_lim_reglement='".$this->db->idate($this->date_lim_reglement)."'";
			}
			$sql.= ' WHERE rowid = '.$this->id;

			dol_syslog(get_class($this)."::validate", LOG_DEBUG);
			$resql=$this->db->query($sql);
			if (! $resql)
			{
				dol_print_error($this->db);
				$error=89;
			}

			// On verifie si la facture etait une provisoire
			if (! $error && (preg_match('/^[\(]?PROV/i', $this->ref)))
			{
				// La verif qu'une remise n'est pas utilisee 2 fois est faite au moment de l'insertion de ligne
			}

			if (! $error)
			{
				// Define third party as a customer
				$result=$this->thirdparty->set_as_client();

				// Si active on decremente le produit principal et ses composants a la validation de facture

				if (
					$this->type != self::TYPE_DEPOSIT && $result >= 0
					&& !empty($conf->stock->enabled) && !empty($conf->global->STOCK_CALCULATE_ON_BILL) && $idwarehouse > 0)
				{
					require_once DOL_DOCUMENT_ROOT.'/product/stock/class/mouvementstock.class.php';
					$langs->load("agenda");

						//vamos acrear stockmouvementdoc
					require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementdocext.class.php';
					$objectdoc = new Stockmouvementdocext($this->db);

					$numref = $objectdoc->getNextNumRef($soc);
						//creamos el registro principal
					$objectdoc->ref = $numref;
					$objectdoc->entity = $conf->entity;
					$objectdoc->fk_entrepot_from = 0;
					$objectdoc->fk_entrepot_to = $idwarehouse;
					$objectdoc->fk_type_mov = $fk_type_mov+0;
					$objectdoc->fk_type_mov = 3;
					$objectdoc->datem = dol_now();
					$objectdoc->label = $this->newref;
					$objectdoc->date_create = dol_now();
					$objectdoc->date_mod = dol_now();
					$objectdoc->tms = dol_now();
					$objectdoc->model_pdf = 'outputalm';
					$objectdoc->fk_user_create = $user->id;
					$objectdoc->fk_user_mod = $user->id;
					$objectdoc->statut = 1;
					$resdoc = $objectdoc->create($user);
					if ($resdoc <=0)
					{
						setEventMessages($objectdoc->error,$objectdoc->errors,'errors');
						$error=97;
					}

						// Loop on each line
					$cpt=count($this->lines);
					for ($i = 0; $i < $cpt; $i++)
					{
						if ($this->lines[$i]->fk_product > 0)
						{
							$mouvP = new MouvementStock($this->db);
							$mouvP->origin = &$this;
								// We decrease stock for product
							if (
								$this->type == self::TYPE_CREDIT_NOTE)
							{
								$result=$mouvP->reception($user, $this->lines[$i]->fk_product, $idwarehouse, $this->lines[$i]->qty, 0, $langs->trans("InvoiceValidatedInDolibarr",$num));
								if ($result < 0) {
									$error=98;
								}
							}
							else
							{
										//vamos a realizar la valoración de la salida por el metodo de valoración definido
								$nbpiece = $this->lines[$i]->qty;
								if (is_numeric($nbpiece))
								{
									require_once DOL_DOCUMENT_ROOT.'/sales/class/productext.class.php';
									require_once DOL_DOCUMENT_ROOT.'/almacen/class/mouvementstockext.class.php';
									require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementaddext.class.php';
									require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementtype.class.php';
									$typemethod = $conf->global->ALMACEN_METHOD_VALUATION_INVENTORY;
									$objectadd = new Stockmouvementaddext($this->db);
									$objsmt = new Stockmouvementtype($this->db);
									$transf = new Productext($this->db);
									$resultn=$transf->fetch($this->lines[$i]->fk_product);
									$transf->load_stock();
									$pricesrc=0;
									if (isset($transf->stock_warehouse[$idwarehouse]->pmp)) $pricesrc=$transf->stock_warehouse[$idwarehouse]->pmp;
									if (empty($pricesrc)) $pricesrc = $transf->pmp;

									$pricedest=$pricesrc;

									$aSales = array();
											//valuacion por el metodo peps
									$objMouvement = new MouvementStockext($this->db);
									$date = dol_now();
									$resmov = $objMouvement->get_value_product($idwarehouse,$date,$this->lines[$i]->fk_product,$nbpiece,$typemethod,$pricesrc,$transf);
									if ($resmov <= 0)
									{
										$error=99;
										setEventMessages($langs->trans('Error en obtener el movimiento..').' '.$resmov,null,'errors');
									}
									$aSales = $objMouvement->aSales;
									if ($lTransfer) $aProductval[$object->fk_product] = $aSales;
												//echo '<hr>opcion1 ';

									foreach ((array) $aSales AS $fk_stock => $row)
									{
											//$transf->origin = 'facturedet';
											//$transf->originid = $this->lines[$i]->id;
										$transf->origin = 'facture';
										$transf->originid = $this->id;
												// Add stock
										$result2=$transf->add_transfer_ok($user,$idwarehouse,$row['qty'],1,$this->newref.' '.$this->label,$pricedest,$this->lines[$i]->id,3);
										if ($result2 <= 0)
										{
											$error=101;
											setEventMessage($product->error, 'errors');
										}
										$fk_entrepot_to = $idwarehouse;
										$aIdsdes[$result2] = $result2;

												//buscamos y actualizamos registro en stock_mouvement_add
										$resadd = $objectadd->fetch(0,$result2);
										if ($resadd==0)
										{
													//echo '<hr>xcreaing '.$result2;
											$now = dol_now();
											$objectadd->fk_stock_mouvement = $result2;
											$objectadd->fk_stock_mouvement_doc = $resdoc;
											$objectadd->period_year = ($_SESSION['period_year']?$_SESSION['period_year']:date('Y'));
											$objectadd->period_month = ($_SESSION['period_month']?$_SESSION['period_month']:date('m'));
											$objectadd->fk_facture = $this->id;
											$objectadd->fk_facturedet = $this->lines[$i]->id;
											$objectadd->fk_user_create = $user->id;
											$objectadd->fk_user_mod = $user->id;
											$objectadd->fk_parent_line = $row['id']+0;
											$objectadd->qty = 0;
											$objectadd->date_create = $now;
											$objectadd->date_mod = $now;
											$objectadd->tms = $now;
											$objectadd->balance_peps = 0;
											$objectadd->balance_ueps = 0;
											$objectadd->value_peps = $row['value'];
											$objectadd->value_ueps = 0;
											$objectadd->value_peps_adq = $row['value'];
											$objectadd->value_ueps_adq = 0;
											$objectadd->status = 1;
											$resadd = $objectadd->create($user);
											if ($resadd <=0)
											{
												setEventMessages($objectadd->error,$objectadd->errors,'errors');
												$error=102;
											}
										}
										elseif($resadd==1)
										{
													//echo '<hr>actualizqaing '.$objectadd->id;
											$now = dol_now();
											$objectadd->fk_user_mod = $user->id;
											$objectadd->fk_stock_mouvement_doc = $resdoc;
											$objectadd->period_year = ($_SESSION['period_year']?$_SESSION['period_year']:date('Y'));
											$objectadd->period_month = ($_SESSION['period_month']?$_SESSION['period_month']:date('m'));
											$objectadd->date_mod = $now;
											$objectadd->tms = $now;
											$objectadd->fk_facture = $this->id;
											$objectadd->fk_facturedet = $this->lines[$i]->id;
											$objectadd->fk_parent_line = $row['id']+0;
											$objectadd->qty = 0;
											$objectadd->balance_peps = 0;
											$objectadd->balance_ueps = 0;
											$objectadd->value_peps = $row['value'];
											$objectadd->value_ueps = 0;
											$objectadd->value_peps_adq = $row['value'];
											$objectadd->value_ueps_adq = 0;
											$objectadd->status = 1;
											$resadd = $objectadd->update($user);
											if ($resadd<=0)
											{
												setEventMessages($objectadd->error,$objectadd->errors,'errors');
												$error=103;
											}
										}

												//creamos registro en stock_mouvement_type
										$objsmt->fk_stock_mouvement = $result2;
										$objsmt->fk_type_mouvement = $object->fk_type_mov;
										$objsmt->fk_type_mouvement = 3;
										$objsmt->tms = dol_now();
										$objsmt->statut = 1;
										$resmt = $objsmt->create($user);
										if ($resmt <= 0)
										{
											$error=104;
											setEventMessages($objsmt->error,$objsmt->errors,'errors');
										}
									}
								}
							}
									//$result=$mouvP->livraison($user, $this->lines[$i]->fk_product, $idwarehouse, $this->lines[$i]->qty, $this->lines[$i]->subprice, $langs->trans("InvoiceValidatedInDolibarr",$num));
						}
					}
				}
			}

					// Trigger calls
			if (! $error && ! $notrigger)
			{
	    	        	// Call trigger
				$result=$this->call_trigger('BILL_VALIDATE',$user);
				if ($result < 0) $error=999;
	        		    // End call triggers
			}

			if (! $error)
			{
				$this->oldref = $this->ref;

						// Rename directory if dir was a temporary ref
				if (preg_match('/^[\(]?PROV/i', $this->ref))
				{
							// Rename of object directory ($this->ref = old ref, $num = new ref)
							// to  not lose the linked files
					$oldref = dol_sanitizeFileName($this->ref);
					$newref = dol_sanitizeFileName($num);
					$dirsource = $conf->facture->dir_output.'/'.$oldref;
					$dirdest = $conf->facture->dir_output.'/'.$newref;
					if (file_exists($dirsource))
					{
						dol_syslog(get_class($this)."::validate rename dir ".$dirsource." into ".$dirdest);

						if (@rename($dirsource, $dirdest))
						{
							dol_syslog("Rename ok");
									// Rename docs starting with $oldref with $newref
							$listoffiles=dol_dir_list($conf->facture->dir_output.'/'.$newref, 'files', 1, '^'.preg_quote($oldref,'/'));
							foreach($listoffiles as $fileentry)
							{
								$dirsource=$fileentry['name'];
								$dirdest=preg_replace('/^'.preg_quote($oldref,'/').'/',$newref, $dirsource);
								$dirsource=$fileentry['path'].'/'.$dirsource;
								$dirdest=$fileentry['path'].'/'.$dirdest;
								@rename($dirsource, $dirdest);
							}
						}
					}
				}
			}

			if (! $error && !$this->is_last_in_cycle())
			{
				if (! $this->updatePriceNextInvoice($langs))
				{
					$error=1001;
				}
			}

					// Set new ref and define current statut
			if (! $error)
			{
				$this->ref = $num;
				$this->facnumber=$num;
				$this->statut= self::STATUS_VALIDATED;
				$this->brouillon=0;
				$this->date_validation=$now;
				$i = 0;

				if (!empty($conf->global->INVOICE_USE_SITUATION))
				{
					$final = True;
					$nboflines = count($this->lines);
					while (($i < $nboflines) && $final) {
						$final = ($this->lines[$i]->situation_percent == 100);
						$i++;
					}
					if ($final) {
						$this->setFinal($user);
					}
				}
			}
		}
		else
		{
			$error++;
		}
			//echo $error;exit;
		if (! $error)
		{
			$this->db->commit();
			return 1;
		}
		else
		{
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 *      Return clicable link of object (with eventually picto)
	 *
	 *      @param  int     $withpicto       Add picto into link
	 *      @param  string  $option          Where point the link
	 *      @param  int     $max             Maxlength of ref
	 *      @param  int     $short           1=Return just URL
	 *      @param  string  $moretitle       Add more text to title tooltip
	 *      @return string                   String with URL
	 */
	function getNomUrladd($withpicto=0,$option='',$max=0,$short=0,$moretitle='')
	{
		global $langs, $conf;

		$result='';

		if ($option == 'withdraw') $url = DOL_URL_ROOT.'/sales/compta/facture/prelevement.php?facid='.$this->id;
		else $url = DOL_URL_ROOT.'/sales/compta/facture.php?facid='.$this->id;

		if ($short) return $url;

		$picto='bill';
		if ($this->type == self::TYPE_REPLACEMENT) $picto.='r'; // Replacement invoice
		if ($this->type == self::TYPE_CREDIT_NOTE) $picto.='a'; // Credit note
		if ($this->type == self::TYPE_DEPOSIT) $picto.='d'; // Deposit invoice

		$label = '<u>' . $langs->trans("ShowInvoice") . '</u>';
		if (! empty($this->ref))
			$label .= '<br><b>'.$langs->trans('Ref') . ':</b> ' . $this->ref;
		if (! empty($this->ref_client))
			$label .= '<br><b>' . $langs->trans('RefCustomer') . ':</b> ' . $this->ref_client;
		if (! empty($this->total_ht))
			$label.= '<br><b>' . $langs->trans('AmountHT') . ':</b> ' . price($this->total_ht, 0, $langs, 0, -1, -1, $conf->currency);
		if (! empty($this->total_tva))
			$label.= '<br><b>' . $langs->trans('VAT') . ':</b> ' . price($this->total_tva, 0, $langs, 0, -1, -1, $conf->currency);
		if (! empty($this->total_ttc))
			$label.= '<br><b>' . $langs->trans('AmountTTC') . ':</b> ' . price($this->total_ttc, 0, $langs, 0, -1, -1, $conf->currency);
		if ($this->type == self::TYPE_REPLACEMENT) $label=$langs->transnoentitiesnoconv("ShowInvoiceReplace").': '.$this->ref;
			if ($this->type == self::TYPE_CREDIT_NOTE) $label=$langs->transnoentitiesnoconv("ShowInvoiceAvoir").': '.$this->ref;
				if ($this->type == self::TYPE_DEPOSIT) $label=$langs->transnoentitiesnoconv("ShowInvoiceDeposit").': '.$this->ref;
					if ($this->type == self::TYPE_SITUATION) $label=$langs->transnoentitiesnoconv("ShowInvoiceSituation").': '.$this->ref;
						if ($moretitle) $label.=' - '.$moretitle;

						$linkstart='<a href="'.$url.'" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip">';
						$linkend='</a>';

						if ($withpicto) $result.=($linkstart.img_object(($max?dol_trunc($label,$max):$label), $picto, 'class="classfortooltip"').$linkend);
							if ($withpicto && $withpicto != 2) $result.=' ';
							if ($withpicto != 2) $result.=$linkstart.($max?dol_trunc($this->ref,$max):$this->ref).$linkend;
								return $result;
							}

							function getLinesArrayadd()
							{
								$this->fetch_linesadd();
							}

	/**
	 *  Load all detailed lines into this->lines
	 *
	 *  @return     int         1 if OK, < 0 if KO
	 */
	function fetch_linesadd()
	{
		$this->lines=array();

		$sql = 'SELECT l.rowid, l.fk_product, l.fk_parent_line, l.label as custom_label, l.description, l.product_type, l.price, l.qty, l.tva_tx, ';
		$sql.= ' l.situation_percent, l.fk_prev_id,';
		$sql.= ' l.localtax1_tx, l.localtax2_tx, l.localtax1_type, l.localtax2_type, l.remise_percent, l.remise, l.fk_remise_except, l.subprice, l.price, ';
		$sql.= ' l.rang, l.special_code,';
		$sql.= ' l.date_start as date_start, l.date_end as date_end,';
		$sql.= ' l.info_bits, l.total_ht, l.total_tva, l.total_localtax1, l.total_localtax2, l.total_ttc, l.fk_code_ventilation, l.fk_product_fournisseur_price as fk_fournprice, l.buy_price_ht as pa_ht,';
		$sql.= ' l.fk_unit,';
		$sql.= ' l.fk_multicurrency, l.multicurrency_code, l.multicurrency_subprice, l.multicurrency_total_ht, l.multicurrency_total_tva, l.multicurrency_total_ttc,';
		$sql.= ' p.ref as product_ref, p.fk_product_type as fk_product_type, p.label as product_label, p.description as product_desc';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'facturedet as l';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON l.fk_product = p.rowid';
		$sql.= ' WHERE l.fk_facture = '.$this->id;
		$sql.= ' ORDER BY l.rang, l.rowid';

		dol_syslog(get_class($this).'::fetch_lines', LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;
			while ($i < $num)
			{
				$objp = $this->db->fetch_object($result);
				$line = new FactureLigne($this->db);

				$line->id               = $objp->rowid;
				$line->rowid            = $objp->rowid;             // deprecated
				$line->label            = $objp->custom_label;      // deprecated
				$line->desc             = $objp->description;       // Description line
				$line->description      = $objp->description;       // Description line
				$line->product_type     = $objp->product_type;      // Type of line
				$line->ref              = $objp->product_ref;       // Ref product
				$line->product_ref      = $objp->product_ref;       // Ref product
				$line->libelle          = $objp->product_label;     // TODO deprecated
				$line->product_label    = $objp->product_label;     // Label product
				$line->product_desc     = $objp->product_desc;      // Description product
				$line->fk_product_type  = $objp->fk_product_type;   // Type of product
				$line->qty              = $objp->qty;
				$line->subprice         = $objp->subprice;
				$line->price            = $objp->price;
				$line->tva_tx           = $objp->tva_tx;
				$line->localtax1_tx     = $objp->localtax1_tx;
				$line->localtax2_tx     = $objp->localtax2_tx;
				$line->localtax1_type   = $objp->localtax1_type;
				$line->localtax2_type   = $objp->localtax2_type;
				$line->remise_percent   = $objp->remise_percent;
				$line->remise           = $objp->remise;
				$line->fk_remise_except = $objp->fk_remise_except;
				$line->fk_product       = $objp->fk_product;
				$line->date_start       = $this->db->jdate($objp->date_start);
				$line->date_end         = $this->db->jdate($objp->date_end);
				$line->date_start       = $this->db->jdate($objp->date_start);
				$line->date_end         = $this->db->jdate($objp->date_end);
				$line->info_bits        = $objp->info_bits;
				$line->total_ht         = $objp->total_ht;
				$line->total_tva        = $objp->total_tva;
				$line->total_localtax1  = $objp->total_localtax1;
				$line->total_localtax2  = $objp->total_localtax2;
				$line->total_ttc        = $objp->total_ttc;
				$line->code_ventilation = $objp->fk_code_ventilation;
				$line->fk_fournprice    = $objp->fk_fournprice;
				$marginInfos            = getMarginInfos($objp->subprice, $objp->remise_percent, $objp->tva_tx, $objp->localtax1_tx, $objp->localtax2_tx, $line->fk_fournprice, $objp->pa_ht);
				$line->pa_ht            = $marginInfos[0];
				$line->marge_tx         = $marginInfos[1];
				$line->marque_tx        = $marginInfos[2];
				$line->rang             = $objp->rang;
				$line->special_code     = $objp->special_code;
				$line->fk_parent_line   = $objp->fk_parent_line;
				$line->situation_percent= $objp->situation_percent;
				$line->fk_prev_id       = $objp->fk_prev_id;
				$line->fk_unit          = $objp->fk_unit;

				// Multicurrency
				$line->fk_multicurrency         = $objp->fk_multicurrency;
				$line->multicurrency_code       = $objp->multicurrency_code;
				$line->multicurrency_subprice   = $objp->multicurrency_subprice;
				$line->multicurrency_total_ht   = $objp->multicurrency_total_ht;
				$line->multicurrency_total_tva  = $objp->multicurrency_total_tva;
				$line->multicurrency_total_ttc  = $objp->multicurrency_total_ttc;

				$this->lines[$i] = $line;

				$i++;
			}
			$this->db->free($result);
			return 1;
		}
		else
		{
			$this->error=$this->db->error();
			return -3;
		}
	}

	/**
	 *  Return HTML table for object lines
	 *  TODO Move this into an output class file (htmlline.class.php)
	 *  If lines are into a template, title must also be into a template
	 *  But for the moment we don't know if it'st possible as we keep a method available on overloaded objects.
	 *
	 *  @param  string      $action             Action code
	 *  @param  string      $seller             Object of seller third party
	 *  @param  string      $buyer              Object of buyer third party
	 *  @param  int         $selected           Object line selected
	 *  @param  int         $dateSelector       1=Show also date range input fields
	 *  @return void
	 */
	function printObjectLinesadd($action, $seller, $buyer, $selected=0, $dateSelector=0)
	{
		global $conf, $hookmanager, $langs, $user;
		// TODO We should not use global var for this !
		global $inputalsopricewithtax, $usemargins, $disableedit, $disablemove, $disableremove;

		// Define usemargins
		$usemargins=0;
		if (! empty($conf->margin->enabled) && ! empty($this->element) && in_array($this->element,array('facture','propal','commande'))) $usemargins=1;

		print '<tr class="liste_titre nodrag nodrop">';

		if (! empty($conf->global->MAIN_VIEW_LINE_NUMBER)) print '<td class="linecolnum" align="center" width="5">&nbsp;</td>';

		// Description
		print '<td class="linecoldescription">'.$langs->trans('Description').'</td>';

		if ($this->element == 'supplier_proposal')
		{
			print '<td class="linerefsupplier" align="right"><span id="title_fourn_ref">'.$langs->trans("SupplierProposalRefFourn").'</span></td>';
		}

		// VAT
		print '<td class="linecolvat" align="right" width="50">'.$langs->trans('VAT').'</td>';

		// Price HT
		print '<td class="linecoluht" align="right" width="80">'.$langs->trans('PriceUHT').'</td>';

		// Multicurrency
		if (!empty($conf->multicurrency->enabled)) print '<td class="linecoluht_currency" align="right" width="80">'.$langs->trans('PriceUHTCurrency').'</td>';

		if ($inputalsopricewithtax) print '<td align="right" width="80">'.$langs->trans('PriceUTTC').'</td>';

		// Qty
		print '<td class="linecolqty" align="right">'.$langs->trans('Qty').'</td>';

		if($conf->global->PRODUCT_USE_UNITS)
		{
			print '<td class="linecoluseunit" align="left">'.$langs->trans('Unit').'</td>';
		}

		// Reduction short
		print '<td class="linecoldiscount" align="right">'.$langs->trans('ReductionShort').'</td>';

		if ($this->situation_cycle_ref) {
			print '<td class="linecolcycleref" align="right">' . $langs->trans('Progress') . '</td>';
		}

		if ($usemargins && ! empty($conf->margin->enabled) && empty($user->societe_id))
		{
			if ($conf->global->MARGIN_TYPE == "1")
				print '<td class="linecolmargin1 margininfos" align="right" width="80">'.$langs->trans('BuyingPrice').'</td>';
			else
				print '<td class="linecolmargin1 margininfos" align="right" width="80">'.$langs->trans('CostPrice').'</td>';

			if (! empty($conf->global->DISPLAY_MARGIN_RATES) && $user->rights->margins->liretous)
				print '<td class="linecolmargin2 margininfos" align="right" width="50">'.$langs->trans('MarginRate').'</td>';
			if (! empty($conf->global->DISPLAY_MARK_RATES) && $user->rights->margins->liretous)
				print '<td class="linecolmargin2 margininfos" align="right" width="50">'.$langs->trans('MarkRate').'</td>';
		}
		//ice
		print '<td class="linecolht" align="right">'.$langs->trans('ICE').'</td>';
		// Total HT
		print '<td class="linecolht" align="right">'.$langs->trans('TotalHTShort').'</td>';

		// Multicurrency
		if (!empty($conf->multicurrency->enabled)) print '<td class="linecoltotalht_currency" align="right">'.$langs->trans('TotalHTShortCurrency').'</td>';

		print '<td class="linecoledit"></td>';
		// No width to allow autodim

		print '<td class="linecoldelete" width="10"></td>';

		print '<td class="linecolmove" width="10"></td>';

		print "</tr>\n";

		$num = count($this->lines);
		$var = true;
		$i   = 0;

		//Line extrafield
		require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafieldsline = new ExtraFields($this->db);
		$extralabelslines=$extrafieldsline->fetch_name_optionals_label($this->table_element_line);

		foreach ($this->lines as $line)
		{
			//Line extrafield
			$line->fetch_optionals($line->id,$extralabelslines);

			$var=!$var;

			//if (is_object($hookmanager) && (($line->product_type == 9 && ! empty($line->special_code)) || ! empty($line->fk_parent_line)))
			if (is_object($hookmanager))   // Old code is commented on preceding line.
			{
				if (empty($line->fk_parent_line))
				{
					$parameters = array('line'=>$line,'var'=>$var,'num'=>$num,'i'=>$i,'dateSelector'=>$dateSelector,'seller'=>$seller,'buyer'=>$buyer,'selected'=>$selected, 'extrafieldsline'=>$extrafieldsline);
					$reshook = $hookmanager->executeHooks('printObjectLine', $parameters, $this, $action);    // Note that $action and $object may have been modified by some hooks
				}
				else
				{
					$parameters = array('line'=>$line,'var'=>$var,'num'=>$num,'i'=>$i,'dateSelector'=>$dateSelector,'seller'=>$seller,'buyer'=>$buyer,'selected'=>$selected, 'extrafieldsline'=>$extrafieldsline);
					$reshook = $hookmanager->executeHooks('printObjectSubLine', $parameters, $this, $action);    // Note that $action and $object may have been modified by some hooks
				}
			}
			if (empty($reshook))
			{
				//if ($conf->global->PRICE_TAXES_INCLUDED)
				//  $line->price = price2num($line->total_ttc / $line->qty,'MU');
				$this->printObjectLineadd($action,$line,$var,$num,$i,$dateSelector,$seller,$buyer,$selected,$extrafieldsline);
			}
			$i++;
		}
	}

	/**
	 *  Return HTML content of a detail line
	 *  TODO Move this into an output class file (htmlline.class.php)
	 *
	 *  @param  string      $action             GET/POST action
	 *  @param CommonObjectLine $line               Selected object line to output
	 *  @param  string      $var                Is it a an odd line (true)
	 *  @param  int         $num                Number of line (0)
	 *  @param  int         $i                  I
	 *  @param  int         $dateSelector       1=Show also date range input fields
	 *  @param  string      $seller             Object of seller third party
	 *  @param  string      $buyer              Object of buyer third party
	 *  @param  int         $selected           Object line selected
	 *  @param  int         $extrafieldsline    Object of extrafield line attribute
	 *  @return void
	 */
	function printObjectLineadd($action,$line,$var,$num,$i,$dateSelector,$seller,$buyer,$selected=0,$extrafieldsline=0)
	{
		global $conf,$langs,$user,$object,$hookmanager;
		global $form,$bc,$bcdd;
		global $object_rights, $disableedit, $disablemove;   // TODO We should not use global var for this !

		$object_rights = $this->getRights();

		$element=$this->element;

		$text=''; $description=''; $type=0;

		// Show product and description
		$type=(! empty($line->product_type)?$line->product_type:$line->fk_product_type);
		// Try to enhance type detection using date_start and date_end for free lines where type was not saved.
		if (! empty($line->date_start)) $type=1; // deprecated
		if (! empty($line->date_end)) $type=1; // deprecated

		// Ligne en mode visu
		if ($action != 'editline' || $selected != $line->id)
		{
			// Product
			if ($line->fk_product > 0)
			{
				$product_static = new Product($this->db);
				$product_static->fetch($line->fk_product);

				$product_static->ref = $line->ref; //can change ref in hook
				$product_static->label = $line->label; //can change label in hook
				$text=$product_static->getNomUrl(1);

				// Define output language and label
				if (! empty($conf->global->MAIN_MULTILANGS))
				{
					if (! is_object($this->thirdparty))
					{
						dol_print_error('','Error: Method printObjectLine was called on an object and object->fetch_thirdparty was not done before');
						return;
					}

					$prod = new Product($this->db);
					$prod->fetch($line->fk_product);

					$outputlangs = $langs;
					$newlang='';
					if (empty($newlang) && GETPOST('lang_id')) $newlang=GETPOST('lang_id');
					if (! empty($conf->global->PRODUIT_TEXTS_IN_THIRDPARTY_LANGUAGE) && empty($newlang)) $newlang=$this->thirdparty->default_lang;      // For language to language of customer
					if (! empty($newlang))
					{
						$outputlangs = new Translate("",$conf);
						$outputlangs->setDefaultLang($newlang);
					}

					$label = (! empty($prod->multilangs[$outputlangs->defaultlang]["label"])) ? $prod->multilangs[$outputlangs->defaultlang]["label"] : $line->product_label;
				}
				else
				{
					$label = $line->product_label;
				}

				$text.= ' - '.(! empty($line->label)?$line->label:$label);
				$description.=(! empty($conf->global->PRODUIT_DESC_IN_FORM)?'':dol_htmlentitiesbr($line->description)); // Description is what to show on popup. We shown nothing if already into desc.
			}
			$line->pu_ttc = price2num($line->subprice * (1 + ($line->tva_tx/100)), 'MU');
			$line->pu_ttc = price2num($line->total_ttc+$line->remise / $line->qty,'MU');
			$line->pu_ttc = price2num($line->price);
			$line->discount = price2num($line->remise);
			// Output template part (modules that overwrite templates must declare this into descriptor)
			// Use global variables + $dateSelector + $seller and $buyer
			$dirtpls=array_merge($conf->modules_parts['tpl'],array('/core/tpl2'));
			foreach($dirtpls as $reldir)
			{
				$tpl = dol_buildpath($reldir.'/objectline_view.tpl.php');
				if (empty($conf->file->strict_mode)) {
					$res=@include $tpl;
				} else {
					$res=include $tpl; // for debug
				}
				if ($res) break;
			}
		}

		// Ligne en mode update
		if ($this->statut == 0 && $action == 'editline' && $selected == $line->id)
		{
			$label = (! empty($line->label) ? $line->label : (($line->fk_product > 0) ? $line->product_label : ''));
			if (! empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $placeholder=' placeholder="'.$langs->trans("Label").'"';
			else $placeholder=' title="'.$langs->trans("Label").'"';

			$line->pu_ttc = price2num($line->subprice * (1 + ($line->tva_tx/100)), 'MU');
			$line->pu_ttc = price2num($line->total_ttc / $line->qty,'MU');
			$line->pu_ttc = $line->price;
			$line->discount = $line->remise;
			// Output template part (modules that overwrite templates must declare this into descriptor)
			// Use global variables + $dateSelector + $seller and $buyer
			$dirtpls=array_merge($conf->modules_parts['tpl'],array('/core/tpl2'));
			foreach($dirtpls as $reldir)
			{
				$tpl = dol_buildpath($reldir.'/objectline_edit.tpl.php');
				if (empty($conf->file->strict_mode)) {
					$res=@include $tpl;
				} else {
					$res=include $tpl; // for debug
				}
				if ($res) break;
			}
		}
	}

	/* This is to show add lines */

	/**
	 *  Show add free and predefined products/services form
	 *
	 *  @param  int             $dateSelector       1=Show also date range input fields
	 *  @param  Societe         $seller             Object thirdparty who sell
	 *  @param  Societe         $buyer              Object thirdparty who buy
	 *  @return void
	 */
	function formAddObjectLineadd($dateSelector,$seller,$buyer)
	{
		global $conf,$user,$langs,$object,$hookmanager;
		global $form,$bcnd,$var;

		//Line extrafield
		require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafieldsline = new ExtraFields($this->db);
		$extralabelslines=$extrafieldsline->fetch_name_optionals_label($this->table_element_line);

		// Output template part (modules that overwrite templates must declare this into descriptor)
		// Use global variables + $dateSelector + $seller and $buyer
		$dirtpls=array_merge($conf->modules_parts['tpl'],array('/core/tpl2'));
		foreach($dirtpls as $reldir)
		{
			$tpl = dol_buildpath($reldir.'/objectline_create.tpl.php');
			if (empty($conf->file->strict_mode)) {
				$res=@include $tpl;
			} else {
				$res=include $tpl; // for debug
			}
			if ($res) break;
		}
	}
	/**
	 *      Add an invoice line into database (linked to product/service or not).
	 *      Les parametres sont deja cense etre juste et avec valeurs finales a l'appel
	 *      de cette methode. Aussi, pour le taux tva, il doit deja avoir ete defini
	 *      par l'appelant par la methode get_default_tva(societe_vendeuse,societe_acheteuse,produit)
	 *      et le desc doit deja avoir la bonne valeur (a l'appelant de gerer le multilangue)
	 *
	 *      @param      string      $desc               Description of line
	 *      @param      double      $pu_ht              Unit price without tax (> 0 even for credit note)
	 *      @param      double      $qty                Quantity
	 *      @param      double      $txtva              Force vat rate, -1 for auto
	 *      @param      double      $txlocaltax1        Local tax 1 rate (deprecated)
	 *      @param      double      $txlocaltax2        Local tax 2 rate (deprecated)
	 *      @param      int         $fk_product         Id of predefined product/service
	 *      @param      double      $remise_percent     Percent of discount on line
	 *      @param      int $date_start         Date start of service
	 *      @param      int $date_end           Date end of service
	 *      @param      int         $ventil             Code of dispatching into accountancy
	 *      @param      int         $info_bits          Bits de type de lignes
	 *      @param      int         $fk_remise_except   Id discount used
	 *      @param      string      $price_base_type    'HT' or 'TTC'
	 *      @param      double      $pu_ttc             Unit price with tax (> 0 even for credit note)
	 *      @param      int         $type               Type of line (0=product, 1=service). Not used if fk_product is defined, the type of product is used.
	 *      @param      int         $rang               Position of line
	 *      @param      int         $special_code       Special code (also used by externals modules!)
	 *      @param      string      $origin             'order', ...
	 *      @param      int         $origin_id          Id of origin object
	 *      @param      int         $fk_parent_line     Id of parent line
	 *      @param      int         $fk_fournprice      Supplier price id (to calculate margin) or ''
	 *      @param      int         $pa_ht              Buying price of line (to calculate margin) or ''
	 *      @param      string      $label              Label of the line (deprecated, do not use)
	 *      @param      array       $array_options      extrafields array
	 *      @param      int         $situation_percent  Situation advance percentage
	 *      @param      int         $fk_prev_id         Previous situation line id reference
	 *      @param      string      $fk_unit            Code of the unit to use. Null to use the default one
	 *      @return     int                             <0 if KO, Id of line if OK
	 */
	function addlineadd($desc, $pu_ht, $qty, $txtva, $txlocaltax1=0, $txlocaltax2=0, $fk_product=0, $remise_percent=0, $date_start='', $date_end='', $ventil=0, $info_bits=0, $fk_remise_except='', $price_base_type='HT', $pu_ttc=0, $type=self::TYPE_STANDARD, $rang=-1, $special_code=0, $origin='', $origin_id=0, $fk_parent_line=0, $fk_fournprice=null, $pa_ht=0, $label='', $array_options=0, $situation_percent=100, $fk_prev_id='', $fk_unit = null, $lines)
	{
			// Deprecation warning
		if ($label) {
			dol_syslog(__METHOD__ . ": using line label is deprecated", LOG_WARNING);
		}

		global $mysoc, $conf, $langs;

		dol_syslog(get_class($this)."::addlineadd facid=$this->id,desc=$desc,pu_ht=$pu_ht,qty=$qty,txtva=$txtva, txlocaltax1=$txlocaltax1, txlocaltax2=$txlocaltax2, fk_product=$fk_product,remise_percent=$remise_percent,date_start=$date_start,date_end=$date_end,ventil=$ventil,info_bits=$info_bits,fk_remise_except=$fk_remise_except,price_base_type=$price_base_type,pu_ttc=$pu_ttc,type=$type, fk_unit=$fk_unit", LOG_DEBUG);
		include_once DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php';

			// Clean parameters
		if (empty($remise_percent)) $remise_percent=0;
		if (empty($lines->remise)) $lines->remise=0;
		if (empty($qty)) $qty=0;
		if (empty($info_bits)) $info_bits=0;
		if (empty($rang)) $rang=0;
		if (empty($ventil)) $ventil=0;
		if (empty($txtva)) $txtva=0;
		if (empty($txlocaltax1)) $txlocaltax1=0;
		if (empty($txlocaltax2)) $txlocaltax2=0;
		if (empty($fk_parent_line) || $fk_parent_line < 0) $fk_parent_line=0;
		if (empty($fk_prev_id)) $fk_prev_id = 'null';
		if (! isset($situation_percent) || $situation_percent > 100 || (string) $situation_percent == '') $situation_percent = 100;

		$remise_percent=price2num($remise_percent);
		$remise=price2num($remise);
		$qty=price2num($qty);
		$pu_ht=price2num($pu_ht);
		$pu_ttc=price2num($pu_ttc);
		$pa_ht=price2num($pa_ht);
		$txtva=price2num($txtva);
		$txlocaltax1=price2num($txlocaltax1);
		$txlocaltax2=price2num($txlocaltax2);

		if ($price_base_type=='HT')
		{
			$pu=$pu_ht;
		}
		else
		{
			$pu=$pu_ttc;
		}

			// Check parameters
		if ($type < 0) return -1;

		if (! empty($this->brouillon))
		{
			$this->db->begin();

			$product_type=$type;
			if (!empty($fk_product))
			{
				$product=new Product($this->db);
				$result=$product->fetch($fk_product);
				$product_type=$product->type;

				if (! empty($conf->global->STOCK_MUST_BE_ENOUGH_FOR_INVOICE) && $product_type == 0 && $product->stock_reel < $qty) {
					$langs->load("errors");
					$this->error=$langs->trans('ErrorStockIsNotEnoughToAddProductOnInvoice', $product->ref);
					$this->db->rollback();
					return -3;
				}
			}

			// Calcul du total TTC et de la TVA pour la ligne a partir de
			// qty, pu, remise_percent et txtva
			// TRES IMPORTANT: C'est au moment de l'insertion ligne qu'on doit stocker
			// la part ht, tva et ttc, et ce au niveau de la ligne qui a son propre taux tva.

			//$localtaxes_type=getLocalTaxesFromRate($txtva,0,$this->thirdparty, $mysoc);
			//$txtva = preg_replace('/\s*\(.*\)/','',$txtva);  // Remove code into vatrate.

			//$tabprice = calcul_price_total($qty, $pu, $remise_percent, $txtva, $txlocaltax1, $txlocaltax2, 0, $price_base_type, $info_bits, $product_type, $mysoc, $localtaxes_type, $situation_percent, $this->multicurrency_tx);

			$total_ht  = $tabprice[0];
			$total_tva = $tabprice[1];
			$total_ttc = $tabprice[2];
			$total_localtax1 = $tabprice[9];
			$total_localtax2 = $tabprice[10];
			//$pu_ht = $tabprice[3];

			// MultiCurrency
			$multicurrency_total_ht  = $tabprice[16];
			$multicurrency_total_tva = $tabprice[17];
			$multicurrency_total_ttc = $tabprice[18];

			$total_ht  = price2num($lines->total_ht,'MT');
			$total_tva = price2num($lines->total_tva,'MT');
			$total_ttc = price2num($lines->total_ttc,'MT');
			$total_localtax1 = price2num($lines->total_localtax1)+0;
			$total_localtax2 = price2num($lines->total_localtax2);
			if (empty($total_localtax2)) $total_localtax2 =0;
			$localtax1_type = $lines->localtax1_type;
			$localtax2_type = $lines->localtax2_type;
			$remise = $lines->remise+0;


			// MultiCurrency
			$multicurrency_total_ht  = $tabprice[16];
			$multicurrency_total_tva = $tabprice[17];
			$multicurrency_total_ttc = $tabprice[18];

			// Rank to use
			$rangtouse = $rang;
			if ($rangtouse == -1)
			{
				$rangmax = $this->line_max($fk_parent_line);
				$rangtouse = $rangmax + 1;
			}
			// Insert line
			$this->line=new FactureLigneext($this->db);

			$this->line->context = $this->context;

			$this->line->fk_facture=$this->id;
			$this->line->label=$label;
			// deprecated
			$this->line->desc=$desc;

			$this->line->qty=            ($this->type==self::TYPE_CREDIT_NOTE?abs($qty):$qty);
			   // For credit note, quantity is always positive and unit price negative
			$this->line->subprice=       ($this->type==self::TYPE_CREDIT_NOTE?-abs($pu_ht):$pu_ht);
			// For credit note, unit price always negative, always positive otherwise
			$this->line->price = ($this->type==self::TYPE_CREDIT_NOTE?-abs($pu_ttc):$pu_ttc);
			$this->line->vat_src_code = $lines->vat_src_code;
			$this->line->tva_tx=$txtva;
			$this->line->localtax1_tx=$txlocaltax1;
			$this->line->localtax2_tx=$txlocaltax2;
			$this->line->localtax1_type = $localtax1_type;
			$this->line->localtax2_type = $localtax2_type;

			$this->line->total_ht=       (($this->type==self::TYPE_CREDIT_NOTE||$qty<0)?-abs($total_ht):$total_ht);
			// For credit note and if qty is negative, total is negative
			$this->line->total_ttc=      (($this->type==self::TYPE_CREDIT_NOTE||$qty<0)?-abs($total_ttc):$total_ttc);
			// For credit note and if qty is negative, total is negative
			$this->line->total_tva=      (($this->type==self::TYPE_CREDIT_NOTE||$qty<0)?-abs($total_tva):$total_tva);
			 // For credit note and if qty is negative, total is negative
			$this->line->total_localtax1=(($this->type==self::TYPE_CREDIT_NOTE||$qty<0)?-abs($total_localtax1):$total_localtax1);
			// For credit note and if qty is negative, total is negative
			$this->line->total_localtax2=(($this->type==self::TYPE_CREDIT_NOTE||$qty<0)?-abs($total_localtax2):$total_localtax2);
			// For credit note and if qty is negative, total is negative

			$this->line->fk_product=$fk_product;
			$this->line->product_type=$product_type;
			$this->line->remise_percent=$remise_percent;
			$this->line->remise=$remise;
			$this->line->date_start=$date_start;
			$this->line->date_end=$date_end;
			$this->line->ventil=$ventil;
			$this->line->rang=$rangtouse;
			$this->line->info_bits=$info_bits;
			$this->line->fk_remise_except=$fk_remise_except;

			$this->line->special_code=$special_code;
			$this->line->fk_parent_line=$fk_parent_line;
			$this->line->origin=$origin;
			$this->line->origin_id=$origin_id;
			$this->line->situation_percent = $situation_percent;
			$this->line->fk_prev_id = $fk_prev_id;
			$this->line->fk_unit=$fk_unit;

			// infos marge
			$this->line->fk_fournprice = $fk_fournprice;
			$this->line->pa_ht = $pa_ht;

			// Multicurrency
			$this->line->fk_multicurrency           = $this->fk_multicurrency;
			$this->line->multicurrency_code         = $this->multicurrency_code;
			$this->line->multicurrency_subprice     = price2num($this->line->subprice * $this->multicurrency_tx)+0;
			$this->line->multicurrency_total_ht     = $multicurrency_total_ht+0;
			$this->line->multicurrency_total_tva    = $multicurrency_total_tva+0;
			$this->line->multicurrency_total_ttc    = $multicurrency_total_ttc+0;

			if (is_array($array_options) && count($array_options)>0) {
				$this->line->array_options=$array_options;
			}

			$result=$this->line->insertadd();
			if ($result > 0)
			{
				// Reorder if child line
				if (! empty($fk_parent_line)) $this->line_order(true,'DESC');

				// Mise a jour informations denormalisees au niveau de la facture meme
				//$result=$this->update_price(1,'auto',0,$mysoc);   // The addline method is designed to add line from user input so total calculation with update_price must be done using 'auto' mode.
				if ($result > 0)
				{
					$this->db->commit();
					return $this->line->rowid;
				}
				else
				{
					$this->error=$this->db->error();
					$this->db->rollback();
					return -1;
				}
			}
			else
			{
				$this->error=$this->line->error;
				$this->db->rollback();
				return -2;
			}
		}
	}

	/**
	 *  Update a detail line
	 *
	 *  @param      int         $rowid              Id of line to update
	 *  @param      string      $desc               Description of line
	 *  @param      double      $pu                 Prix unitaire (HT ou TTC selon price_base_type) (> 0 even for credit note lines)
	 *  @param      double      $qty                Quantity
	 *  @param      double      $remise_percent     Pourcentage de remise de la ligne
	 *  @param      int     $date_start         Date de debut de validite du service
	 *  @param      int     $date_end           Date de fin de validite du service
	 *  @param      double      $txtva              VAT Rate
	 *  @param      double      $txlocaltax1        Local tax 1 rate
	 *  @param      double      $txlocaltax2        Local tax 2 rate
	 *  @param      string      $price_base_type    HT or TTC
	 *  @param      int         $info_bits          Miscellaneous informations
	 *  @param      int         $type               Type of line (0=product, 1=service)
	 *  @param      int         $fk_parent_line     Id of parent line (0 in most cases, used by modules adding sublevels into lines).
	 *  @param      int         $skip_update_total  Keep fields total_xxx to 0 (used for special lines by some modules)
	 *  @param      int         $fk_fournprice      Id of origin supplier price
	 *  @param      int         $pa_ht              Price (without tax) of product when it was bought
	 *  @param      string      $label              Label of the line (deprecated, do not use)
	 *  @param      int         $special_code       Special code (also used by externals modules!)
	 *  @param      array       $array_options      extrafields array
	 *  @param      int         $situation_percent  Situation advance percentage
	 *  @param      string      $fk_unit            Code of the unit to use. Null to use the default one
	 *  @return     int                             < 0 if KO, > 0 if OK
	 */
	function updatelineadd($rowid, $desc, $pu, $qty, $remise_percent, $date_start, $date_end, $txtva, $txlocaltax1=0, $txlocaltax2=0, $price_base_type='HT', $info_bits=0, $type= self::TYPE_STANDARD, $fk_parent_line=0, $skip_update_total=0, $fk_fournprice=null, $pa_ht=0, $label='', $special_code=0, $array_options=0, $situation_percent=0, $fk_unit = null,$lines)
	{
		global $conf,$user;
		// Deprecation warning
		if ($label) {
			dol_syslog(__METHOD__ . ": using line label is deprecated", LOG_WARNING);
		}

		include_once DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php';

		global $mysoc,$langs;

		dol_syslog(get_class($this)."::updatelineadd rowid=$rowid, desc=$desc, pu=$pu, qty=$qty, remise_percent=$remise_percent, date_start=$date_start, date_end=$date_end, txtva=$txtva, txlocaltax1=$txlocaltax1, txlocaltax2=$txlocaltax2, price_base_type=$price_base_type, info_bits=$info_bits, type=$type, fk_parent_line=$fk_parent_line pa_ht=$pa_ht, special_code=$special_code fk_unit=$fk_unit", LOG_DEBUG);

		if ($this->brouillon)
		{
			if (!$this->is_last_in_cycle() && empty($this->error))
			{
				if (!$this->checkProgressLine($rowid, $situation_percent))
				{
					if (!$this->error) $this->error=$langs->trans('invoiceLineProgressError');
					return -3;
				}
			}

			$this->db->begin();

			// Clean parameters
			if (empty($qty)) $qty=0;
			if (empty($fk_parent_line) || $fk_parent_line < 0) $fk_parent_line=0;
			if (empty($special_code) || $special_code == 3) $special_code=0;
			if (! isset($situation_percent) || $situation_percent > 100 || (string) $situation_percent == '') $situation_percent = 100;

			$remise_percent = price2num($remise_percent);
			$qty            = price2num($qty);
			$pu             = price2num($pu);
			$pa_ht          = price2num($pa_ht);
			$pu_ht          = price2num($pu_ht);
			$pu_ttc         = price2num($pu_ttc);
			$txtva          = price2num($txtva);
			$txlocaltax1    = price2num($txlocaltax1);
			$txlocaltax2    = price2num($txlocaltax2);
			if (empty($txlocaltax1)) $txlocaltax1=0;
			if (empty($txlocaltax2)) $txlocaltax2=0;
			$txtva          = price2num($txtva);
			// Check parameters
			if ($type < 0) return -1;

			// Calculate total with, without tax and tax from qty, pu, remise_percent and txtva
			// TRES IMPORTANT: C'est au moment de l'insertion ligne qu'on doit stocker
			// la part ht, tva et ttc, et ce au niveau de la ligne qui a son propre taux tva.

			//$localtaxes_type=getLocalTaxesFromRate($txtva,0,$this->thirdparty, $mysoc);
			//$txtva = preg_replace('/\s*\(.*\)/','',$txtva);  // Remove code into vatrate.

			//$tabprice=calcul_price_total($qty, $pu, $remise_percent, $txtva, $txlocaltax1, $txlocaltax2, 0, $price_base_type, $info_bits, $type, $mysoc, $localtaxes_type, $situation_percent, $this->multicurrency_tx);

			$total_ht  = $tabprice[0];
			$total_tva = $tabprice[1];
			$total_ttc = $tabprice[2];
			$total_localtax1=$tabprice[9];
			$total_localtax2=$tabprice[10];
			//$pu_ht  = $tabprice[3];
			$pu_tva = $tabprice[4];
			//$pu_ttc = $tabprice[5];

			$total_ht  = $lines->total_ht;
			$total_tva = $lines->total_tva;
			$total_ttc = $lines->total_ttc;
			$total_localtax1=$lines->total_localtax1;
			$total_localtax2=$lines->total_localtax2;
			$localtax1_type = $lines->localtax1_type;
			$localtax2_type = $lines->localtax2_type;
			$remise = $lines->remise;
			$remise_percent = $lines->remise_percent;
			$pu_ht = $lines->subprice;
			$pu_ttc = $lines->price;
			//$pu_ht  = $tabprice[3];
			//$pu_tva = $tabprice[4];
			//$pu_ttc = $tabprice[5];

			// MultiCurrency
			//$multicurrency_total_ht  = $tabprice[16];
			//$multicurrency_total_tva = $tabprice[17];
			//$multicurrency_total_ttc = $tabprice[18];

			// Old properties: $price, $remise (deprecated)
			//$price = $pu;
			//$remise = 0;
			//if ($remise_percent > 0)
			//{
			//  $remise = round(($pu * $remise_percent / 100),2);
			//  $price = ($pu - $remise);
			//}
			//$price    = price2num($price);

			//Fetch current line from the database and then clone the object and set it in $oldline property
			$line = new FactureLigneext($this->db);
			$line->fetch($rowid);

			if (!empty($line->fk_product))
			{
				$product=new Product($this->db);
				$result=$product->fetch($line->fk_product);
				$product_type=$product->type;

				if (! empty($conf->global->STOCK_MUST_BE_ENOUGH_FOR_INVOICE) && $product_type == 0 && $product->stock_reel < $qty) {
					$langs->load("errors");
					$this->error=$langs->trans('ErrorStockIsNotEnoughToAddProductOnInvoice', $product->ref);
					$this->db->rollback();
					return -3;
				}
			}

			$staticline = clone $line;

			$line->oldline = $staticline;
			$this->line = $line;
			$this->line->context = $this->context;

			// Reorder if fk_parent_line change
			if (! empty($fk_parent_line) && ! empty($staticline->fk_parent_line) && $fk_parent_line != $staticline->fk_parent_line)
			{
				$rangmax = $this->line_max($fk_parent_line);
				$this->line->rang = $rangmax + 1;
			}

			$this->line->rowid              = $rowid;
			$this->line->label              = $label;
			$this->line->desc               = $desc;
			$this->line->qty                = ($this->type==self::TYPE_CREDIT_NOTE?abs($qty):$qty);
			// For credit note, quantity is always positive and unit price negative
			$this->line->tva_tx             = $txtva;
			$this->line->localtax1_tx       = $txlocaltax1;
			$this->line->localtax2_tx       = $txlocaltax2;
			$this->line->localtax1_type     = $localtax1_type;
			$this->line->localtax2_type     = $localtax2_type;
			$this->line->remise_percent     = $remise_percent;
			$this->line->remise             = $remise;
			$this->line->subprice           = ($this->type==2?-abs($pu_ht):$pu_ht);
			// For credit note, unit price always negative, always positive otherwise
			$this->line->price              = $pu_ttc;
			$this->line->date_start         = $date_start;
			$this->line->date_end           = $date_end;
			$this->line->total_ht           = (($this->type==self::TYPE_CREDIT_NOTE||$qty<0)?-abs($total_ht):$total_ht);
			// For credit note and if qty is negative, total is negative
			$this->line->total_tva          = (($this->type==self::TYPE_CREDIT_NOTE||$qty<0)?-abs($total_tva):$total_tva);
			$this->line->total_localtax1    = $total_localtax1;
			$this->line->total_localtax2    = $total_localtax2;
			$this->line->total_ttc          = (($this->type==self::TYPE_CREDIT_NOTE||$qty<0)?-abs($total_ttc):$total_ttc);
			$this->line->info_bits          = $info_bits;
			$this->line->special_code       = $special_code;
			$this->line->product_type       = $type;
			$this->line->fk_parent_line     = $fk_parent_line;
			$this->line->skip_update_total  = $skip_update_total;
			$this->line->situation_percent  = $situation_percent;
			$this->line->fk_unit            = $fk_unit;

			$this->line->fk_fournprice = $fk_fournprice;
			$this->line->pa_ht = $pa_ht;

			// Multicurrency
			$this->line->multicurrency_subprice     = price2num($this->line->subprice * $this->multicurrency_tx);
			$this->line->multicurrency_total_ht     = $multicurrency_total_ht+0;
			$this->line->multicurrency_total_tva    = $multicurrency_total_tva+0;
			$this->line->multicurrency_total_ttc    = $multicurrency_total_ttc+0;

			if (is_array($array_options) && count($array_options)>0) {
				$this->line->array_options=$array_options;
			}

			$result=$this->line->updateadd($user);
			if ($result > 0)
			{
				// Reorder if child line
				if (! empty($fk_parent_line)) $this->line_order(true,'DESC');

				// Mise a jour info denormalisees au niveau facture
				//$this->update_price(1);
				$this->db->commit();
				return $result;
			}
			else
			{
				$this->error=$this->line->error;
				$this->db->rollback();
				return -1;
			}
		}
		else
		{
			$this->error="Invoice statut makes operation forbidden";
			return -2;
		}
	}

	public function update_total()
	{
		global $conf,$user;

		$error=0;

		$pa_ht_isemptystring = (empty($this->pa_ht) && $this->pa_ht == ''); // If true, we can use a default value. If this->pa_ht = '0', we must use '0'.

		// Clean parameters
		if (empty($this->total)) $this->total=0;
		if (empty($this->tva)) $this->tva=0;
		if (empty($this->localtax1)) $this->localtax1=0;
		if (empty($this->localtax2)) $this->localtax2=0;
		if (empty($this->total_ttc)) $this->total_ttc=0;
		if (empty($this->multicurrency_total_ht)) $this->multicurrency_total_ht=0;
		if (empty($this->multicurrency_total_tva)) $this->multicurrency_total_tva=0;
		if (empty($this->multicurrency_total_ttc)) $this->multicurrency_total_ttc=0;
		if (empty($this->remise)) $this->remise=0;  // TODO A virer

		$this->db->begin();

		// Mise a jour ligne en base
		$sql = "UPDATE ".MAIN_DB_PREFIX."facture SET";
		$sql.= " tva='".price2num($this->tva)."'";
		$sql.= " , localtax1=".price2num($this->localtax1);
		$sql.= " , localtax2=".price2num($this->localtax2);
		$sql.= " , total=".price2num($this->total);
		$sql.= " , total_ttc=".price2num($this->total_ttc);
		$sql.= " , multicurrency_total_ttc=".price2num($this->multicurrency_total_ttc);
		$sql.= " , multicurrency_total_ht=".price2num($this->multicurrency_total_ht);
		$sql.= " , multicurrency_total_tva=".price2num($this->multicurrency_total_tva);
		$sql.= " , remise=".price2num($this->remise)."";                // TODO A virer

		$sql.= " WHERE rowid = ".$this->id;

		dol_syslog(get_class($this)."::update_total", LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$this->db->commit();
			return 1;
		}
		else
		{
			$this->error=$this->db->error();
			$this->db->rollback();
			return -2;
		}
	}
	/**
	 *      Update database
	 *
	 *      @param      User	$user        	User that modify
	 *      @param      int		$notrigger	    0=launch triggers after, 1=disable triggers
	 *      @return     int      			   	<0 if KO, >0 if OK
	 */
	function update_modelpdf($user=null, $notrigger=0)
	{
		$error=0;

		// Clean parameters

		if (isset($this->modelpdf)) $this->modelpdf=trim($this->modelpdf);


		// Check parameters
		// Put here code to add control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."facture SET";

		$sql.= " model_pdf=".(isset($this->modelpdf)?"'".$this->db->escape($this->modelpdf)."'":"null");

		$sql.= " WHERE rowid=".$this->id;

		$this->db->begin();

		dol_syslog(get_class($this)."::update", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$error++; $this->errors[]="Error ".$this->db->lasterror();
		}

		if (! $error)
		{
			if (! $notrigger)
			{
				// Call trigger
				//$result=$this->call_trigger('BILL_MODIFY',$user);
				//if ($result < 0) $error++;
				// End call triggers
			}
		}

		// Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
				dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
				$this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
	}

}

class Factureligneext extends FactureLigne
{

	var $aData;
	function get_sum_taxes($id)
	{
		if (empty($id)) return -1;

		$sql = 'SELECT fd.rowid, fd.fk_facture, fd.fk_parent_line, fd.fk_product, fd.product_type, fd.label as custom_label, fd.description, fd.price, fd.qty, fd.tva_tx,';
		$sql.= ' fd.localtax1_tx, fd. localtax2_tx, fd.remise, fd.remise_percent, fd.fk_remise_except, fd.subprice,';
		$sql.= ' fd.date_start as date_start, fd.date_end as date_end, fd.fk_product_fournisseur_price as fk_fournprice, fd.buy_price_ht as pa_ht,';
		$sql.= ' fd.info_bits, fd.special_code, fd.total_ht, fd.total_tva, fd.total_ttc, fd.total_localtax1, fd.total_localtax2, fd.rang,';
		$sql.= ' fd.fk_code_ventilation,';
		$sql.= ' fd.fk_unit,';
		$sql.= ' fd.situation_percent, fd.fk_prev_id,';
		$sql.= ' p.ref as product_ref, p.label as product_libelle, p.description as product_desc';
		$sql.= ' , fd.multicurrency_subprice';
		$sql.= ' , fd.multicurrency_total_ht';
		$sql.= ' , fd.multicurrency_total_tva';
		$sql.= ' , fd.multicurrency_total_ttc';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'facturedet as fd';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON fd.fk_product = p.rowid';
		$sql.= ' WHERE fd.fk_facture = '.$id;

		$resql = $this->db->query($sql);
		$this->aData = array();
		if ($resql)
		{
			$i = 0;
			$num = $this->db->num_rows($resql);
			while ($i < $num)
			{
				$objp = $this->db->fetch_object($resql);
				$this->aData['total_ht']+= $objp->total_ht;
				$this->aData['total_ttc']+= $objp->total_ttc;
				$this->aData['total_tva']+= $objp->total_tva;
				$this->aData['total_localtax1']+= $objp->total_localtax1;
				$this->aData['total_localtax2']+= $objp->total_localtax2;
				$this->aData['multicurrency_total_ht']+= $objp->multicurrency_total_ht;
				$this->aData['multicurrency_total_ttc']+= $objp->multicurrency_total_ttc;
				$this->aData['multicurrency_total_tva']+= $objp->multicurrency_total_tva;
				$i++;
			}
			$this->db->free($resql);
			return $num;
		}
		else
			return -1;
	}

	/**
	 *  Insert line into database
	 *
	 *  @param      int     $notrigger      1 no triggers
	 *  @return     int                     <0 if KO, >0 if OK
	 */
	function insertadd($notrigger=0)
	{
		global $langs,$user,$conf;

		$error=0;

		//echo '<hr>ht '.$this->total_ht.' tva '.$this->total_tva.' ttc '.$this->total_ttc;

		$pa_ht_isemptystring = (empty($this->pa_ht) && $this->pa_ht == ''); // If true, we can use a default value. If this->pa_ht = '0', we must use '0'.

		dol_syslog(get_class($this)."::insert rang=".$this->rang, LOG_DEBUG);

		// Clean parameters
		$this->desc=trim($this->desc);
		if (empty($this->tva_tx)) $this->tva_tx=0;
		if (empty($this->localtax1_tx)) $this->localtax1_tx=0;
		if (empty($this->localtax2_tx)) $this->localtax2_tx=0;
		if (empty($this->localtax1_type)) $this->localtax1_type=0;
		if (empty($this->localtax2_type)) $this->localtax2_type=0;
		if (empty($this->total_localtax1)) $this->total_localtax1=0;
		if (empty($this->total_localtax2)) $this->total_localtax2=0;
		if (empty($this->rang)) $this->rang=0;
		if (empty($this->remise_percent)) $this->remise_percent=0;
		if (empty($this->remise)) $this->remise=0;
		if (empty($this->info_bits)) $this->info_bits=0;
		if (empty($this->subprice)) $this->subprice=0;
		if (empty($this->price)) $this->price=0;
		if (empty($this->product_type)) $this->product_type=0;
		if (empty($this->special_code)) $this->special_code=0;
		if (empty($this->fk_parent_line)) $this->fk_parent_line=0;
		if (empty($this->fk_prev_id)) $this->fk_prev_id = 'null';
		if (! isset($this->situation_percent) || $this->situation_percent > 100 || (string) $this->situation_percent == '') $this->situation_percent = 100;

		if (empty($this->pa_ht)) $this->pa_ht=0;
		if (empty($this->multicurrency_subprice)) $this->multicurrency_subprice=0;
		if (empty($this->multicurrency_total_ht)) $this->multicurrency_total_ht=0;
		if (empty($this->multicurrency_total_tva)) $this->multicurrency_total_tva=0;
		if (empty($this->multicurrency_total_ttc)) $this->multicurrency_total_ttc=0;

		// if buy price not defined, define buyprice as configured in margin admin
		if ($this->pa_ht == 0 && $pa_ht_isemptystring)
		{
			if (($result = $this->defineBuyPrice($this->subprice, $this->remise_percent, $this->fk_product)) < 0)
			{
				return $result;
			}
			else
			{
				$this->pa_ht = $result;
			}
		}

		// Check parameters
		if ($this->product_type < 0)
		{
			$this->error='ErrorProductTypeMustBe0orMore';
			return -1;
		}
		if (! empty($this->fk_product))
		{
			// Check product exists
			$result=Product::isExistingObject('product', $this->fk_product);
			if ($result <= 0)
			{
				$this->error='ErrorProductIdDoesNotExists';
				return -1;
			}
		}

		$this->db->begin();

		// Insertion dans base de la ligne
		$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'facturedet';
		$sql.= ' (fk_facture, fk_parent_line, label, description, qty,';
		$sql.= ' vat_src_code,';
		$sql.= ' tva_tx, localtax1_tx, localtax2_tx, localtax1_type, localtax2_type,';
		$sql.= ' fk_product, product_type, remise_percent, subprice, fk_remise_except,';
		$sql.= ' date_start, date_end, fk_code_ventilation, ';
		$sql.= ' rang, special_code, fk_product_fournisseur_price, buy_price_ht,';
		$sql.= ' info_bits, total_ht, total_tva, total_ttc, total_localtax1, total_localtax2,';
		$sql.= ' situation_percent, fk_prev_id,';
		$sql.= ' fk_unit';
		$sql.= ' , price';
		$sql.= ' , remise';
		$sql.= ', fk_multicurrency, multicurrency_code, multicurrency_subprice, multicurrency_total_ht, multicurrency_total_tva, multicurrency_total_ttc';
		$sql.= ')';
		$sql.= " VALUES (".$this->fk_facture.",";
		$sql.= " ".($this->fk_parent_line>0?"'".$this->fk_parent_line."'":"null").",";
		$sql.= " ".(! empty($this->label)?"'".$this->db->escape($this->label)."'":"null").",";
		$sql.= " '".$this->db->escape($this->desc)."',";
		$sql.= " ".price2num($this->qty).",";
		$sql.= ' '.(! empty($this->vat_src_code)?"'".$this->vat_src_code."'":"null").',';
		$sql.= " ".price2num($this->tva_tx).",";
		$sql.= " ".price2num($this->localtax1_tx).",";
		$sql.= " ".price2num($this->localtax2_tx).",";
		$sql.= " '".$this->localtax1_type."',";
		$sql.= " '".$this->localtax2_type."',";
		$sql.= ' '.(! empty($this->fk_product)?$this->fk_product:"null").',';
		$sql.= " ".$this->product_type.",";
		$sql.= " ".price2num($this->remise_percent).",";
		$sql.= " ".price2num($this->subprice).",";
		$sql.= ' '.(! empty($this->fk_remise_except)?$this->fk_remise_except:"null").',';
		$sql.= " ".(! empty($this->date_start)?"'".$this->db->idate($this->date_start)."'":"null").",";
		$sql.= " ".(! empty($this->date_end)?"'".$this->db->idate($this->date_end)."'":"null").",";
		$sql.= ' '.$this->fk_code_ventilation.',';
		$sql.= ' '.$this->rang.',';
		$sql.= ' '.$this->special_code.',';
		$sql.= ' '.(! empty($this->fk_fournprice)?$this->fk_fournprice:"null").',';
		$sql.= ' '.price2num($this->pa_ht).',';
		$sql.= " '".$this->info_bits."',";
		$sql.= " ".price2num($this->total_ht).",";
		$sql.= " ".price2num($this->total_tva).",";
		$sql.= " ".price2num($this->total_ttc).",";
		$sql.= " ".price2num($this->total_localtax1).",";
		$sql.= " ".price2num($this->total_localtax2);
		$sql .= ", " . $this->situation_percent;
		$sql .= ", " . $this->fk_prev_id;
		$sql .= ", ".(!$this->fk_unit ? 'NULL' : $this->fk_unit);
		$sql .= ", " . $this->price;
		$sql .= ", " . $this->remise;
		$sql.= ", ".(int) $this->fk_multicurrency;
		$sql.= ", '".$this->db->escape($this->multicurrency_code)."'";
		$sql.= ", ".price2num($this->multicurrency_subprice);
		$sql.= ", ".price2num($this->multicurrency_total_ht);
		$sql.= ", ".price2num($this->multicurrency_total_tva);
		$sql.= ", ".price2num($this->multicurrency_total_ttc);
		$sql.= ')';

		dol_syslog(get_class($this)."::insertadd", LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$this->rowid=$this->db->last_insert_id(MAIN_DB_PREFIX.'facturedet');

			if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED)) // For avoid conflicts if trigger used
			{
				$this->id=$this->rowid;
				$result=$this->insertExtraFields();
				if ($result < 0)
				{
					$error++;
				}
			}

			// Si fk_remise_except defini, on lie la remise a la facture
			// ce qui la flague comme "consommee".
			if ($this->fk_remise_except)
			{
				$discount=new DiscountAbsolute($this->db);
				$result=$discount->fetch($this->fk_remise_except);
				if ($result >= 0)
				{
					// Check if discount was found
					if ($result > 0)
					{
						// Check if discount not already affected to another invoice
						if ($discount->fk_facture)
						{
							$this->error=$langs->trans("ErrorDiscountAlreadyUsed",$discount->id);
							dol_syslog(get_class($this)."::insert Error ".$this->error, LOG_ERR);
							$this->db->rollback();
							return -3;
						}
						else
						{
							$result=$discount->link_to_invoice($this->rowid,0);
							if ($result < 0)
							{
								$this->error=$discount->error;
								dol_syslog(get_class($this)."::insert Error ".$this->error, LOG_ERR);
								$this->db->rollback();
								return -3;
							}
						}
					}
					else
					{
						$this->error=$langs->trans("ErrorADiscountThatHasBeenRemovedIsIncluded");
						dol_syslog(get_class($this)."::insert Error ".$this->error, LOG_ERR);
						$this->db->rollback();
						return -3;
					}
				}
				else
				{
					$this->error=$discount->error;
					dol_syslog(get_class($this)."::insert Error ".$this->error, LOG_ERR);
					$this->db->rollback();
					return -3;
				}
			}

			if (! $notrigger)
			{
				// Call trigger
				$result=$this->call_trigger('LINEBILL_INSERT',$user);
				if ($result < 0)
				{
					$this->db->rollback();
					return -2;
				}
				// End call triggers
			}

			$this->db->commit();
			return $this->rowid;

		}
		else
		{
			$this->error=$this->db->error();
			$this->db->rollback();
			return -2;
		}
	}

	/**
	 *  Update line into database
	 *
	 *  @param      User    $user       User object
	 *  @param      int     $notrigger  Disable triggers
	 *  @return     int                 <0 if KO, >0 if OK
	 */
	function updateadd($user='',$notrigger=0)
	{
		global $user,$conf;

		$error=0;

		$pa_ht_isemptystring = (empty($this->pa_ht) && $this->pa_ht == ''); // If true, we can use a default value. If this->pa_ht = '0', we must use '0'.

		// Clean parameters
		$this->desc=trim($this->desc);
		if (empty($this->tva_tx)) $this->tva_tx=0;
		if (empty($this->localtax1_tx)) $this->localtax1_tx=0;
		if (empty($this->localtax2_tx)) $this->localtax2_tx=0;
		if (empty($this->localtax1_type)) $this->localtax1_type=0;
		if (empty($this->localtax2_type)) $this->localtax2_type=0;
		if (empty($this->total_localtax1)) $this->total_localtax1=0;
		if (empty($this->total_localtax2)) $this->total_localtax2=0;
		if (empty($this->remise_percent)) $this->remise_percent=0;
		if (empty($this->remise)) $this->remise=0;
		if (empty($this->info_bits)) $this->info_bits=0;
		if (empty($this->special_code)) $this->special_code=0;
		if (empty($this->product_type)) $this->product_type=0;
		if (empty($this->fk_parent_line)) $this->fk_parent_line=0;
		if (! isset($this->situation_percent) || $this->situation_percent > 100 || (string) $this->situation_percent == '') $this->situation_percent = 100;
		if (empty($this->pa_ht)) $this->pa_ht=0;

		if (empty($this->multicurrency_subprice)) $this->multicurrency_subprice=0;
		if (empty($this->multicurrency_total_ht)) $this->multicurrency_total_ht=0;
		if (empty($this->multicurrency_total_tva)) $this->multicurrency_total_tva=0;
		if (empty($this->multicurrency_total_ttc)) $this->multicurrency_total_ttc=0;

		// Check parameters
		if ($this->product_type < 0) return -1;

		// if buy price not defined, define buyprice as configured in margin admin
		if ($this->pa_ht == 0 && $pa_ht_isemptystring)
		{
			if (($result = $this->defineBuyPrice($this->subprice, $this->remise_percent, $this->fk_product)) < 0)
			{
				return $result;
			}
			else
			{
				$this->pa_ht = $result;
			}
		}

		$this->db->begin();
		// Mise a jour ligne en base
		$sql = "UPDATE ".MAIN_DB_PREFIX."facturedet SET";
		$sql.= " description='".$this->db->escape($this->desc)."'";
		$sql.= ",label=".(! empty($this->label)?"'".$this->db->escape($this->label)."'":"null");
		$sql.= ",subprice=".price2num($this->subprice)."";
		$sql.= ",price=".price2num($this->price)."";
		$sql.= ",remise_percent=".price2num($this->remise_percent)."";
		$sql.= ",remise=".price2num($this->remise)."";

		if ($this->fk_remise_except) $sql.= ",fk_remise_except=".$this->fk_remise_except;
		else $sql.= ",fk_remise_except=null";
		$sql.= ",tva_tx=".price2num($this->tva_tx)."";
		$sql.= ",localtax1_tx=".price2num($this->localtax1_tx)."";
		$sql.= ",localtax2_tx=".price2num($this->localtax2_tx)."";
		$sql.= ",localtax1_type='".$this->localtax1_type."'";
		$sql.= ",localtax2_type='".$this->localtax2_type."'";
		$sql.= ",qty=".price2num($this->qty)."";
		$sql.= ",date_start=".(! empty($this->date_start)?"'".$this->db->idate($this->date_start)."'":"null");
		$sql.= ",date_end=".(! empty($this->date_end)?"'".$this->db->idate($this->date_end)."'":"null");
		$sql.= ",product_type=".$this->product_type;
		$sql.= ",info_bits='".$this->info_bits."'";
		$sql.= ",special_code='".$this->special_code."'";
		if (empty($this->skip_update_total))
		{
			$sql.= ",total_ht=".price2num($this->total_ht)."";
			$sql.= ",total_tva=".price2num($this->total_tva)."";
			$sql.= ",total_ttc=".price2num($this->total_ttc)."";
			$sql.= ",total_localtax1=".price2num($this->total_localtax1)."";
			$sql.= ",total_localtax2=".price2num($this->total_localtax2)."";
		}
		$sql.= " , fk_product_fournisseur_price=".(! empty($this->fk_fournprice)?"'".$this->db->escape($this->fk_fournprice)."'":"null");
		$sql.= " , buy_price_ht='".price2num($this->pa_ht)."'";
		$sql.= ",fk_parent_line=".($this->fk_parent_line>0?$this->fk_parent_line:"null");
		if (! empty($this->rang)) $sql.= ", rang=".$this->rang;
		$sql .= ", situation_percent=" . $this->situation_percent;
		$sql .= ", fk_unit=".(!$this->fk_unit ? 'NULL' : $this->fk_unit);

		// Multicurrency
		$sql.= " , multicurrency_subprice=".price2num($this->multicurrency_subprice)."";
		$sql.= " , multicurrency_total_ht=".price2num($this->multicurrency_total_ht)."";
		$sql.= " , multicurrency_total_tva=".price2num($this->multicurrency_total_tva)."";
		$sql.= " , multicurrency_total_ttc=".price2num($this->multicurrency_total_ttc)."";

		$sql.= " WHERE rowid = ".$this->rowid;

		dol_syslog(get_class($this)."::update", LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED)) // For avoid conflicts if trigger used
			{
				$this->id=$this->rowid;
				$result=$this->insertExtraFields();
				if ($result < 0)
				{
					$error++;
				}
			}

			if (! $notrigger)
			{
				// Call trigger
				$result=$this->call_trigger('LINEBILL_UPDATE',$user);
				if ($result < 0)
				{
					$this->db->rollback();
					return -2;
				}
				// End call triggers
			}
			$this->db->commit();
			return 1;
		}
		else
		{
			$this->error=$this->db->error();
			$this->db->rollback();
			return -2;
		}
	}
}
?>