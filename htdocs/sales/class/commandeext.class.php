<?php
require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';

class Commandeext extends Commande
{

	/**
	 *	Create order
	 *	Note that this->ref can be set or empty. If empty, we will use "(PROV)"
	 *
	 *	@param		User	$user 		Objet user that make creation
	 *	@param		int	    $notrigger	Disable all triggers
	 *	@return 	int			        <0 if KO, >0 if OK
	 */
	function createadd($user, $notrigger=0)
	{
		global $conf,$langs;
		$error=0;

		// Clean parameters
		$this->brouillon = 1;
		// set command as draft

		// Multicurrency (test on $this->multicurrency_tx because we sould take the default rate only if not using origin rate)
		if (!empty($this->multicurrency_code) && empty($this->multicurrency_tx)) list($this->fk_multicurrency,$this->multicurrency_tx) = MultiCurrency::getIdAndTxFromCode($this->db, $this->multicurrency_code);
			else $this->fk_multicurrency = MultiCurrency::getIdFromCode($this->db, $this->multicurrency_code);
				if (empty($this->fk_multicurrency))
				{
					$this->multicurrency_code = $conf->currency;
					$this->fk_multicurrency = 0;
					$this->multicurrency_tx = 1;
				}

				dol_syslog(get_class($this)."::create user=".$user->id);

		// Check parameters
				if (! empty($this->ref))
		// We check that ref is not already used
				{
					$result=self::isExistingObject($this->element, 0, $this->ref);
			// Check ref is not yet used
					if ($result > 0)
					{
						$this->error='ErrorRefAlreadyExists';
						dol_syslog(get_class($this)."::create ".$this->error,LOG_WARNING);
						$this->db->rollback();
						return -1;
					}
				}


				$soc = new Societe($this->db);
				$result=$soc->fetch($this->socid);
				if ($result < 0)
				{
					$this->error="Failed to fetch company";
					dol_syslog(get_class($this)."::create ".$this->error, LOG_ERR);
					return -2;
				}
				if (! empty($conf->global->COMMANDE_REQUIRE_SOURCE) && $this->source < 0)
				{
					$this->error=$langs->trans("ErrorFieldRequired",$langs->trans("Source"));
					dol_syslog(get_class($this)."::create ".$this->error, LOG_ERR);
					return -1;
				}

		// $date_commande is deprecated
				$date = ($this->date_commande ? $this->date_commande : $this->date);

				$now=dol_now();

				$this->db->begin();

				$sql = "INSERT INTO ".MAIN_DB_PREFIX."commande (";
				$sql.= " ref, fk_soc, date_creation, fk_user_author, fk_projet, date_commande, source, note_private, note_public, ref_ext, ref_client, ref_int";
				$sql.= ", model_pdf, fk_cond_reglement, fk_mode_reglement, fk_account, fk_availability, fk_input_reason, date_livraison, fk_delivery_address";
				$sql.= ", fk_shipping_method";
				$sql.= ", fk_warehouse";
				$sql.= ", remise_absolue, remise_percent";
				$sql.= ", fk_incoterms, location_incoterms";
				$sql.= ", entity";
				$sql.= ", fk_multicurrency";
				$sql.= ", multicurrency_code";
				$sql.= ", multicurrency_tx";
				$sql.= ")";
				$sql.= " VALUES ('(PROV)',".$this->socid.", '".$this->db->idate($now)."', ".$user->id;
				$sql.= ", ".($this->fk_project>0?$this->fk_project:"null");
				$sql.= ", '".$this->db->idate($date)."'";
				$sql.= ", ".($this->source>=0 && $this->source != '' ?$this->db->escape($this->source):'null');
				$sql.= ", '".$this->db->escape($this->note_private)."'";
				$sql.= ", '".$this->db->escape($this->note_public)."'";
				$sql.= ", ".($this->ref_ext?"'".$this->db->escape($this->ref_ext)."'":"null");
				$sql.= ", ".($this->ref_client?"'".$this->db->escape($this->ref_client)."'":"null");
				$sql.= ", ".($this->ref_int?"'".$this->db->escape($this->ref_int)."'":"null");
				$sql.= ", '".$this->db->escape($this->modelpdf)."'";
				$sql.= ", ".($this->cond_reglement_id>0?"'".$this->cond_reglement_id."'":"null");
				$sql.= ", ".($this->mode_reglement_id>0?"'".$this->mode_reglement_id."'":"null");
				$sql.= ", ".($this->fk_account>0?$this->fk_account:'NULL');
				$sql.= ", ".($this->availability_id>0?"'".$this->availability_id."'":"null");
				$sql.= ", ".($this->demand_reason_id>0?"'".$this->demand_reason_id."'":"null");
				$sql.= ", ".($this->date_livraison?"'".$this->db->idate($this->date_livraison)."'":"null");
				$sql.= ", ".($this->fk_delivery_address>0?$this->fk_delivery_address:'NULL');
				$sql.= ", ".($this->shipping_method_id>0?$this->shipping_method_id:'NULL');
				$sql.= ", ".($this->warehouse_id>0?$this->warehouse_id:'NULL');
				$sql.= ", ".($this->remise_absolue>0?$this->db->escape($this->remise_absolue):'NULL');
				$sql.= ", ".($this->remise_percent>0?$this->db->escape($this->remise_percent):0);
				$sql.= ", ".(int) $this->fk_incoterms;
				$sql.= ", '".$this->db->escape($this->location_incoterms)."'";
				$sql.= ", ".$conf->entity;
				$sql.= ", ".(int) $this->fk_multicurrency;
				$sql.= ", '".$this->db->escape($this->multicurrency_code)."'";
				$sql.= ", ".(double) $this->multicurrency_tx;
				$sql.= ")";

				dol_syslog(get_class($this)."::create", LOG_DEBUG);
				$resql=$this->db->query($sql);
				if ($resql)
				{
					$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX.'commande');

					if ($this->id)
					{
						$fk_parent_line=0;
						$num=count($this->lines);

				/*
				 *  Insert products details into db
				 */

				for ($i=0;$i<$num;$i++)
				{
					$lines = $this->lines[$i];

					// Test and convert into object this->lines[$i]. When coming from REST API, we may still have an array
					//if (! is_object($line)) $line=json_decode(json_encode($line), FALSE);  // convert recursively array into object.
					if (! is_object($lines)) $lines = (object) $lines;

					// Reset fk_parent_line for no child products and special product
					if (($lines->product_type != 9 && empty($lines->fk_parent_line)) || $lines->product_type == 9) {
						$fk_parent_line = 0;
					}
					if ($conf->fiscal->enabled)
					{

						if ($lines->fk_product>0)
						{
							require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
							$productadd = new Product($this->db);

							$resprod = $productadd->fetch($lines->fk_product);
						}
						$label = (! empty($lines->label) ? $lines->label : '');
						$desc = (! empty($lines->desc) ? $lines->desc : '');
						$product_type = (! empty($lines->product_type) ? $lines->product_type : 0);

						//procesamos el calculo de los impuestos
						$tvacalc = array();
						$tvaht = array();
						$tvattc = array();
						$tvatx = array();
						$k = 1;
						$qty = $lines->qty;
						$pu = $lines->subprice;
						$price_base_type = 'HT';
						if ($conf->global->PRICE_TAXES_INCLUDED)
						{
							$price_base_type = 'TTC';
							$pu = $lines->price;
							if (empty($pu)) $pu = price2num($lines->total_ttc / $lines->qty);
							$lines->price = $pu;
						}
						$fk_unit = $lines->fk_unit;
						$discount = $lines->remise+0;
						$objectadd = new Stdclass();
						$objectadd->code_facture = $conf->global->FISCAL_CODE_FACTURE_SALES;
						$db = $this->db;
						include DOL_DOCUMENT_ROOT.'/fiscal/include/calclinefiscal.inc.php';

						$result = $this->addlineadd($desc, $lines->subprice, $lines->qty, $lines->tva_tx, $lines->localtax1_tx, $lines->localtax2_tx, $lines->fk_product, $lines->remise_percent, $lines->info_bits, $lines->fk_remise_except, $price_base_type, $lines->price, $date_start, $date_end, $product_type, $lines->rang, $lines->special_code, $fk_parent_line, $lines->fk_fournprice, $lines->pa_ht, $label, $array_options, $lines->fk_unit, $object->origin,$lines->rowid,$lines);
					}
					else
					{
						//$result = $object->addline($desc, $lines[$i]->subprice, $lines[$i]->qty, $lines[$i]->tva_tx, $lines[$i]->localtax1_tx, $lines[$i]->localtax2_tx, $lines[$i]->fk_product, $lines[$i]->remise_percent, $lines[$i]->info_bits, $lines[$i]->fk_remise_except, 'HT', 0, $date_start, $date_end, $product_type, $lines[$i]->rang, $lines[$i]->special_code, $fk_parent_line, $lines[$i]->fk_fournprice, $lines[$i]->pa_ht, $label, $array_options, $lines[$i]->fk_unit, $object->origin, $lines[$i]->rowid);
						$result = $this->addline(
							$line->desc,
							$line->subprice,
							$line->qty,
							$line->tva_tx,
							$line->localtax1_tx,
							$line->localtax2_tx,
							$line->fk_product,
							$line->remise_percent,
							$line->info_bits,
							$line->fk_remise_except,
							'HT',
							0,
							$line->date_start,
							$line->date_end,
							$line->product_type,
							$line->rang,
							$line->special_code,
							$fk_parent_line,
							$line->fk_fournprice,
							$line->pa_ht,
							$line->label,
							$line->array_options,
							$line->fk_unit,
							$this->element,
							$line->id
						);

					}




					if ($result < 0)
					{
						if ($result != self::STOCK_NOT_ENOUGH_FOR_ORDER)
							{
								$this->error=$this->db->lasterror();
								dol_print_error($this->db);
							}
							$this->db->rollback();
							return -1;
						}
					// Defined the new fk_parent_line
						if ($result > 0 && $lines->product_type == 9) {
							$fk_parent_line = $result;
						}
					}

					if (!$error && $conf->fiscal->enabled)
					{
						//actualizamos totales en la cabecera
						$res = $this->updatetotal_commande();
					}

					// update ref
					$initialref='(PROV'.$this->id.')';
					if (! empty($this->ref)) $initialref=$this->ref;

					$sql = 'UPDATE '.MAIN_DB_PREFIX."commande SET ref='".$this->db->escape($initialref)."' WHERE rowid=".$this->id;
					if ($this->db->query($sql))
					{
						if ($this->id)
						{
							$this->ref = $initialref;

						// Add object linked
							if (! $error && $this->id && is_array($this->linked_objects) && ! empty($this->linked_objects))
							{
								foreach($this->linked_objects as $origin => $tmp_origin_id)
								{
								if (is_array($tmp_origin_id))       // New behaviour, if linked_object can have several links per type, so is something like array('contract'=>array(id1, id2, ...))
								{
									foreach($tmp_origin_id as $origin_id)
									{
										$ret = $this->add_object_linked($origin, $origin_id);
										if (! $ret)
										{
											dol_print_error($this->db);
											$error++;
										}
									}
								}
								else                                // Old behaviour, if linked_object has only one link per type, so is something like array('contract'=>id1))
								{
									$origin_id = $tmp_origin_id;
									$ret = $this->add_object_linked($origin, $origin_id);
									if (! $ret)
									{
										dol_print_error($this->db);
										$error++;
									}
								}
							}
						}

						if (! $error && $this->id && ! empty($conf->global->MAIN_PROPAGATE_CONTACTS_FROM_ORIGIN) && ! empty($this->origin) && ! empty($this->origin_id))   // Get contact from origin object
						{
							$originforcontact = $this->origin;
							$originidforcontact = $this->origin_id;
							if ($originforcontact == 'shipping')     // shipment and order share the same contacts. If creating from shipment we take data of order
							{
								require_once DOL_DOCUMENT_ROOT . '/expedition/class/expedition.class.php';
								$exp = new Expedition($db);
								$exp->fetch($this->origin_id);
								$exp->fetchObjectLinked();
								if (count($exp->linkedObjectsIds['commande']) > 0)
								{
									foreach ($exp->linkedObjectsIds['commande'] as $key => $value)
									{
										$originforcontact = 'commande';
										$originidforcontact = $value->id;
										break; // We take first one
									}
								}
							}

							$sqlcontact = "SELECT ctc.code, ctc.source, ec.fk_socpeople FROM ".MAIN_DB_PREFIX."element_contact as ec, ".MAIN_DB_PREFIX."c_type_contact as ctc";
							$sqlcontact.= " WHERE element_id = ".$originidforcontact." AND ec.fk_c_type_contact = ctc.rowid AND ctc.element = '".$originforcontact."'";

							$resqlcontact = $this->db->query($sqlcontact);
							if ($resqlcontact)
							{
								while($objcontact = $this->db->fetch_object($resqlcontact))
								{
									//print $objcontact->code.'-'.$objcontact->source.'-'.$objcontact->fk_socpeople."\n";
									$this->add_contact($objcontact->fk_socpeople, $objcontact->code, $objcontact->source);    // May failed because of duplicate key or because code of contact type does not exists for new object
								}
							}
							else dol_print_error($resqlcontact);
						}
					}

					if (! $error)
					{
						$result=$this->insertExtraFields();
						if ($result < 0) $error++;
					}

					if (! $error && ! $notrigger)
					{
						// Call trigger
						$result=$this->call_trigger('ORDER_CREATE',$user);
						if ($result < 0) $error++;
						// End call triggers
					}

					if (! $error)
					{
						$this->db->commit();
						return $this->id;
					}
					else
					{
						$this->db->rollback();
						return -1*$error;
					}
				}
				else
				{
					$this->error=$this->db->lasterror();
					$this->db->rollback();
					return -1;
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

	function updatetotal_commande()
	{
		global $conf,$langs;
		$objecttmp = new OrderLineext($this->db);
		$objecttmp->get_sum_taxes($this->id);
		$aData = $objecttmp->aData;

		//$objtmp = new Commandeext($db);
		//$restmp = $objtmp->fetch($id);
		if ($this->id)
		{
			$this->total_ht 	= $aData['total_ht'];
			$this->tva 		= $aData['total_tva'];
			$this->total_ttc	= $aData['total_ttc'];
			$this->localtax1 	= $aData['total_localtax1'];
			$this->localtax2 	= $aData['total_localtax2'];
			$this->multicurrency_total_ht 	= $aData['multicurrency_total_ht'];
			$this->multicurrency_total_tva	= $aData['multicurrency_total_tva'];
			$this->multicurrency_total_ttc	= $aData['multicurrency_total_ttc'];

			$restot = $this->update_total();
			if ($restot<=0)
			{
				$error++;
				setEventMessages($objtmp->error,$objtmp->errors,'errors');
				$action = '';
			}
		}
		else
		{
			$error++;
			setEventMessages($objtmp->error,$objtmp->errors,'errors');
			$action = '';
		}
		return $error;
	}

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
		global $langs,$conf;
		$sql = 'SELECT c.rowid, c.date_creation, c.ref, c.fk_soc, c.fk_user_author, c.fk_user_valid, c.fk_statut';
		$sql.= ', c.amount_ht, c.total_ht, c.total_ttc, c.tva as total_tva, c.localtax1 as total_localtax1, c.localtax2 as total_localtax2, c.fk_cond_reglement, c.fk_mode_reglement, c.fk_availability, c.fk_input_reason';
		$sql.= ', c.fk_account';
		$sql.= ', c.date_commande';
		$sql.= ', c.date_livraison';
		$sql.= ', c.fk_shipping_method';
		$sql.= ', c.fk_warehouse';
		$sql.= ', c.fk_projet, c.remise_percent, c.remise, c.remise_absolue, c.source, c.facture as billed';
		$sql.= ', c.note_private, c.note_public, c.ref_client, c.ref_ext, c.ref_int, c.model_pdf, c.fk_delivery_address, c.extraparams';
		$sql.= ', c.fk_incoterms, c.location_incoterms';
		$sql.= ", c.fk_multicurrency, c.multicurrency_code, c.multicurrency_tx, c.multicurrency_total_ht, c.multicurrency_total_tva, c.multicurrency_total_ttc";
		$sql.= ", i.libelle as libelle_incoterms";
		$sql.= ', p.code as mode_reglement_code, p.libelle as mode_reglement_libelle';
		$sql.= ', cr.code as cond_reglement_code, cr.libelle as cond_reglement_libelle, cr.libelle_facture as cond_reglement_libelle_doc';
		$sql.= ', ca.code as availability_code, ca.label as availability_label';
		$sql.= ', dr.code as demand_reason_code';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'commande as c';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_payment_term as cr ON (c.fk_cond_reglement = cr.rowid)';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_paiement as p ON (c.fk_mode_reglement = p.id)';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_availability as ca ON (c.fk_availability = ca.rowid)';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_input_reason as dr ON (c.fk_input_reason = ca.rowid)';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_incoterms as i ON c.fk_incoterms = i.rowid';

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
			$sql .= " AND entity IN (" . getEntity("conc", 1) . ")";
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
				$line = new Commande($this->db);

				$line->id                   = $obj->rowid;
				$line->ref                  = $obj->ref;
				$line->ref_client           = $obj->ref_client;
				$line->ref_customer         = $obj->ref_client;
				$line->ref_ext              = $obj->ref_ext;
				$line->ref_int              = $obj->ref_int;
				$line->socid                = $obj->fk_soc;
				$line->statut               = $obj->fk_statut;
				$line->user_author_id       = $obj->fk_user_author;
				$line->user_valid           = $obj->fk_user_valid;
				$line->total_ht             = $obj->total_ht;
				$line->total_tva            = $obj->total_tva;
				$line->total_localtax1      = $obj->total_localtax1;
				$line->total_localtax2      = $obj->total_localtax2;
				$line->total_ttc            = $obj->total_ttc;
				$line->date                 = $this->db->jdate($obj->date_commande);
				$line->date_commande        = $this->db->jdate($obj->date_commande);
				$line->remise               = $obj->remise;
				$line->remise_percent       = $obj->remise_percent;
				$line->remise_absolue       = $obj->remise_absolue;
				$line->source               = $obj->source;
				$line->facturee             = $obj->billed;         // deprecated
				$line->billed               = $obj->billed;
				$line->note                 = $obj->note_private;   // deprecated
				$line->note_private         = $obj->note_private;
				$line->note_public          = $obj->note_public;
				$line->fk_project           = $obj->fk_projet;
				$line->modelpdf             = $obj->model_pdf;
				$line->mode_reglement_id    = $obj->fk_mode_reglement;
				$line->mode_reglement_code  = $obj->mode_reglement_code;
				$line->mode_reglement       = $obj->mode_reglement_libelle;
				$line->cond_reglement_id    = $obj->fk_cond_reglement;
				$line->cond_reglement_code  = $obj->cond_reglement_code;
				$line->cond_reglement       = $obj->cond_reglement_libelle;
				$line->cond_reglement_doc   = $obj->cond_reglement_libelle_doc;
				$line->fk_account           = $obj->fk_account;
				$line->availability_id      = $obj->fk_availability;
				$line->availability_code    = $obj->availability_code;
				$line->availability         = $obj->availability_label;
				$line->demand_reason_id     = $obj->fk_input_reason;
				$line->demand_reason_code   = $obj->demand_reason_code;
				$line->date_livraison       = $this->db->jdate($obj->date_livraison);
				$line->shipping_method_id   = ($obj->fk_shipping_method>0)?$obj->fk_shipping_method:null;
				$line->warehouse_id           = ($obj->fk_warehouse>0)?$obj->fk_warehouse:null;
				$line->fk_delivery_address  = $obj->fk_delivery_address;

				//Incoterms
				$line->fk_incoterms = $obj->fk_incoterms;
				$line->location_incoterms = $obj->location_incoterms;
				$line->libelle_incoterms = $obj->libelle_incoterms;

				// Multicurrency
				$line->fk_multicurrency         = $obj->fk_multicurrency;
				$line->multicurrency_code       = $obj->multicurrency_code;
				$line->multicurrency_tx         = $obj->multicurrency_tx;
				$line->multicurrency_total_ht   = $obj->multicurrency_total_ht;
				$line->multicurrency_total_tva  = $obj->multicurrency_total_tva;
				$line->multicurrency_total_ttc  = $obj->multicurrency_total_ttc;

				$line->extraparams          = (array) json_decode($obj->extraparams, true);

				$line->lines                = array();

				if (
					$line->statut == self::STATUS_DRAFT )
				{
					$this->brouillon = 1;
				}

				require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
				$extrafields=new ExtraFields($this->db);
				$extralabels=$extrafields->fetch_name_optionals_label($this->table_element,true);
				$this->fetch_optionals($this->id,$extralabels);

				if ($lView && $num == 1) $this->fetch($obj->rowid);

				//$result=$this->fetch_lines();
				//if ($result < 0)
				//{
				//    return -3;
				//}

				$this->lines[$line->id] = $line;
			}
			$this->db->free($result);

			return $num;
		}
		else
		{
			$this->error=$this->db->error();
			return -1;
		}
	}


	function getLinesArrayadd()
	{
		$this->fetch_linesadd();
	}
	/**
	 *	Load array lines
	 *
	 *	@param		int		$only_product	Return only physical products
	 *	@return		int						<0 if KO, >0 if OK
	 */
	function fetch_linesadd($only_product=0)
	{
		$this->lines=array();

		$sql = 'SELECT l.rowid, l.fk_product, l.fk_parent_line, l.product_type, l.fk_commande, l.label as custom_label, l.description, l.price, l.qty, l.tva_tx,';
		$sql.= ' l.localtax1_tx, l.localtax2_tx, l.fk_remise_except, l.remise_percent, l.remise, l.subprice, l.price, l.fk_product_fournisseur_price as fk_fournprice, l.buy_price_ht as pa_ht, l.rang, l.info_bits, l.special_code,';
		$sql.= ' l.total_ht, l.total_ttc, l.total_tva, l.total_localtax1, l.total_localtax2, l.date_start, l.date_end,';
		$sql.= ' l.fk_unit,';
		$sql.= ' l.fk_multicurrency, l.multicurrency_code, l.multicurrency_subprice, l.multicurrency_total_ht, l.multicurrency_total_tva, l.multicurrency_total_ttc,';
		$sql.= ' p.ref as product_ref, p.description as product_desc, p.fk_product_type, p.label as product_label,';
		$sql.= ' p.weight, p.weight_units, p.volume, p.volume_units';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'commandedet as l';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON (p.rowid = l.fk_product)';
		$sql.= ' WHERE l.fk_commande = '.$this->id;
		if ($only_product) $sql .= ' AND p.fk_product_type = 0';
		$sql .= ' ORDER BY l.rang, l.rowid';

		dol_syslog(get_class($this)."::fetch_lines", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);

			$i = 0;
			while ($i < $num)
			{
				$objp = $this->db->fetch_object($result);

				$line = new OrderLine($this->db);

				$line->rowid            = $objp->rowid;
				$line->id               = $objp->rowid;
				$line->fk_commande      = $objp->fk_commande;
				$line->commande_id      = $objp->fk_commande;
				$line->label            = $objp->custom_label;
				$line->desc             = $objp->description;
				$line->description      = $objp->description;		// Description line
				$line->product_type     = $objp->product_type;
				$line->qty              = $objp->qty;
				$line->tva_tx           = $objp->tva_tx;
				$line->localtax1_tx     = $objp->localtax1_tx;
				$line->localtax2_tx     = $objp->localtax2_tx;
				$line->total_ht         = $objp->total_ht;
				$line->total_ttc        = $objp->total_ttc;
				$line->total_tva        = $objp->total_tva;
				$line->total_localtax1  = $objp->total_localtax1;
				$line->total_localtax2  = $objp->total_localtax2;
				$line->subprice         = $objp->subprice;
				$line->fk_remise_except = $objp->fk_remise_except;
				$line->remise_percent   = $objp->remise_percent;
				$line->remise 			= $objp->remise;
				$line->price            = $objp->price;
				$line->fk_product       = $objp->fk_product;
				$line->fk_fournprice 	= $objp->fk_fournprice;
				$marginInfos			= getMarginInfos($objp->subprice, $objp->remise_percent, $objp->tva_tx, $objp->localtax1_tx, $objp->localtax2_tx, $line->fk_fournprice, $objp->pa_ht);
				$line->pa_ht 			= $marginInfos[0];
				$line->marge_tx			= $marginInfos[1];
				$line->marque_tx		= $marginInfos[2];
				$line->rang             = $objp->rang;
				$line->info_bits        = $objp->info_bits;
				$line->special_code		= $objp->special_code;
				$line->fk_parent_line	= $objp->fk_parent_line;

				$line->ref				= $objp->product_ref;
				$line->product_ref		= $objp->product_ref;
				$line->libelle			= $objp->product_label;
				$line->product_label	= $objp->product_label;
				$line->product_desc     = $objp->product_desc;
				$line->fk_product_type  = $objp->fk_product_type;	// Produit ou service
				$line->fk_unit          = $objp->fk_unit;

				$line->weight           = $objp->weight;
				$line->weight_units     = $objp->weight_units;
				$line->volume           = $objp->volume;
				$line->volume_units     = $objp->volume_units;

				$line->date_start       = $this->db->jdate($objp->date_start);
				$line->date_end         = $this->db->jdate($objp->date_end);

				// Multicurrency
				$line->fk_multicurrency 		= $objp->fk_multicurrency;
				$line->multicurrency_code 		= $objp->multicurrency_code;
				$line->multicurrency_subprice 	= $objp->multicurrency_subprice;
				$line->multicurrency_total_ht 	= $objp->multicurrency_total_ht;
				$line->multicurrency_total_tva 	= $objp->multicurrency_total_tva;
				$line->multicurrency_total_ttc 	= $objp->multicurrency_total_ttc;

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
	 *	Return clicable link of object (with eventually picto)
	 *
	 *	@param      int			$withpicto      Add picto into link
	 *	@param      int			$option         Where point the link (0=> main card, 1,2 => shipment)
	 *	@param      int			$max          	Max length to show
	 *	@param      int			$short			Use short labels
	 *	@return     string          			String with URL
	 */
		function getNomUrladd($withpicto=0,$option=0,$max=0,$short=0)
		{
			global $conf, $langs, $user;

			$result='';

			if (! empty($conf->expedition->enabled) && ($option == 1 || $option == 2)) $url = DOL_URL_ROOT.'/expedition/shipment.php?id='.$this->id;
			else $url = DOL_URL_ROOT.'/sales/commande/card.php?id='.$this->id;

			if ($short) return $url;

			$picto = 'order';
			$label = '';

			if ($user->rights->commande->lire) {
				$label = '<u>'.$langs->trans("ShowOrder").'</u>';
				if (!empty($this->ref)) {
					$label .= '<br><b>'.$langs->trans('Ref').':</b> '.$this->ref;
				}
				if (!empty($this->ref_client)) {
					$label .= '<br><b>'.$langs->trans('RefCustomer').':</b> '.$this->ref_client;
				}
				if (!empty($this->total_ht)) {
					$label .= '<br><b>'.$langs->trans('AmountHT').':</b> '.price($this->total_ht, 0, $langs, 0, -1, -1,
						$conf->currency);
				}
				if (!empty($this->total_tva)) {
					$label .= '<br><b>'.$langs->trans('VAT').':</b> '.price($this->total_tva, 0, $langs, 0, -1, -1,
						$conf->currency);
				}
				if (!empty($this->total_ttc)) {
					$label .= '<br><b>'.$langs->trans('AmountTTC').':</b> '.price($this->total_ttc, 0, $langs, 0, -1, -1,
						$conf->currency);
				}
			}

			$linkstart = '<a href="'.$url.'" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip">';
			$linkend='</a>';

			if ($withpicto) $result.=($linkstart.img_object($label, $picto, 'class="classfortooltip"').$linkend);
			if ($withpicto && $withpicto != 2) $result.=' ';
			$result.=$linkstart.$this->ref.$linkend;
			return $result;
		}

	/**
	 * 	Return HTML table table of source object lines
	 *  TODO Move this and previous function into output html class file (htmlline.class.php).
	 *  If lines are into a template, title must also be into a template
	 *  But for the moment we don't know if it's possible as we keep a method available on overloaded objects.
	 *
	 *  @return	void
	 */
	function printOriginLinesListadd()
	{
		global $langs, $hookmanager, $conf;

		print '<tr class="liste_titre">';
		print '<td>'.$langs->trans('Ref').'</td>';
		print '<td>'.$langs->trans('Description').'</td>';
		print '<td align="right">'.$langs->trans('VAT').'</td>';
		print '<td align="right">'.$langs->trans('PriceUHT').'</td>';
		if (!empty($conf->multicurrency->enabled)) print '<td align="right">'.$langs->trans('PriceUHTCurrency').'</td>';
		print '<td align="right">'.$langs->trans('Qty').'</td>';
		if($conf->global->PRODUCT_USE_UNITS)
		{
			print '<td align="left">'.$langs->trans('Unit').'</td>';
		}
		print '<td align="right">'.$langs->trans('ReductionShort').'</td></tr>';

		$var = true;
		$i	 = 0;

		foreach ($this->lines as $line)
		{
			$var=!$var;

			if (is_object($hookmanager) && (($line->product_type == 9 && ! empty($line->special_code)) || ! empty($line->fk_parent_line)))
			{
				if (empty($line->fk_parent_line))
				{
					$parameters=array('line'=>$line,'var'=>$var,'i'=>$i);
					$action='';
					$hookmanager->executeHooks('printOriginObjectLine',$parameters,$this,$action);    // Note that $action and $object may have been modified by some hooks
				}
			}
			else
			{
				$this->printOriginLineadd($line,$var);
			}

			$i++;
		}
	}

	/**
	 * 	Return HTML with a line of table array of source object lines
	 *  TODO Move this and previous function into output html class file (htmlline.class.php).
	 *  If lines are into a template, title must also be into a template
	 *  But for the moment we don't know if it's possible as we keep a method available on overloaded objects.
	 *
	 * 	@param	CommonObjectLine	$line		Line
	 * 	@param	string				$var		Var
	 * 	@return	void
	 */
	function printOriginLineadd($line,$var)
	{
		global $langs, $conf;

		//var_dump($line);
		if (!empty($line->date_start))
		{
			$date_start=$line->date_start;
		}
		else
		{
			$date_start=$line->date_debut_prevue;
			if ($line->date_debut_reel) $date_start=$line->date_debut_reel;
		}
		if (!empty($line->date_end))
		{
			$date_end=$line->date_end;
		}
		else
		{
			$date_end=$line->date_fin_prevue;
			if ($line->date_fin_reel) $date_end=$line->date_fin_reel;
		}

		$this->tpl['label'] = '';
		if (! empty($line->fk_parent_line)) $this->tpl['label'].= img_picto('', 'rightarrow');

		if (($line->info_bits & 2) == 2)  // TODO Not sure this is used for source object
		{
			$discount=new DiscountAbsolute($this->db);
			$discount->fk_soc = $this->socid;
			$this->tpl['label'].= $discount->getNomUrl(0,'discount');
		}
		else if (! empty($line->fk_product))
		{
			$productstatic = new Product($this->db);
			$productstatic->id = $line->fk_product;
			$productstatic->ref = $line->ref;
			$productstatic->type = $line->fk_product_type;
			$this->tpl['label'].= $productstatic->getNomUrl(1);
			$this->tpl['label'].= ' - '.(! empty($line->label)?$line->label:$line->product_label);
			// Dates
			if ($line->product_type == 1 && ($date_start || $date_end))
			{
				$this->tpl['label'].= get_date_range($date_start,$date_end);
			}
		}
		else
		{
			$this->tpl['label'].= ($line->product_type == -1 ? '&nbsp;' : ($line->product_type == 1 ? img_object($langs->trans(''),'service') : img_object($langs->trans(''),'product')));
			if (!empty($line->desc)) {
				$this->tpl['label'].=$line->desc;
			}else {
				$this->tpl['label'].= ($line->label ? '&nbsp;'.$line->label : '');
			}
			// Dates
			if ($line->product_type == 1 && ($date_start || $date_end))
			{
				$this->tpl['label'].= get_date_range($date_start,$date_end);
			}
		}

		if (! empty($line->desc))
		{
			if ($line->desc == '(CREDIT_NOTE)')  // TODO Not sure this is used for source object
			{
				$discount=new DiscountAbsolute($this->db);
				$discount->fetch($line->fk_remise_except);
				$this->tpl['description'] = $langs->transnoentities("DiscountFromCreditNote",$discount->getNomUrl(0));
			}
			elseif ($line->desc == '(DEPOSIT)')  // TODO Not sure this is used for source object
			{
				$discount=new DiscountAbsolute($this->db);
				$discount->fetch($line->fk_remise_except);
				$this->tpl['description'] = $langs->transnoentities("DiscountFromDeposit",$discount->getNomUrl(0));
			}
			else
			{
				$this->tpl['description'] = dol_trunc($line->desc,60);
			}
		}
		else
		{
			$this->tpl['description'] = '&nbsp;';
		}

		$this->tpl['vat_rate'] = vatrate($line->tva_tx, true);
		$this->tpl['price'] = price(price2num($line->total_ttc/$line->qty,'MU'));
		$this->tpl['multicurrency_price'] = price($line->multicurrency_subprice);
		$this->tpl['qty'] = (($line->info_bits & 2) != 2) ? $line->qty : '&nbsp;';
		if($conf->global->PRODUCT_USE_UNITS) $this->tpl['unit'] = $line->getLabelOfUnit('long');
		$this->tpl['remise_percent'] = (($line->info_bits & 2) != 2) ? vatrate($line->remise_percent, true) : '&nbsp;';

		// Output template part (modules that overwrite templates must declare this into descriptor)
		// Use global variables + $dateSelector + $seller and $buyer
		$dirtpls=array_merge($conf->modules_parts['tpl'],array('/core/tpl'));
		foreach($dirtpls as $reldir)
		{
			$tpl = dol_buildpath($reldir.'/originproductline.tpl.php');
			if (empty($conf->file->strict_mode)) {
				$res=@include $tpl;
			} else {
				$res=include $tpl; // for debug
			}
			if ($res) break;
		}
	}
	/**
	 *	Return HTML table for object lines
	 *	TODO Move this into an output class file (htmlline.class.php)
	 *	If lines are into a template, title must also be into a template
	 *	But for the moment we don't know if it'st possible as we keep a method available on overloaded objects.
	 *
	 *	@param	string		$action				Action code
	 *	@param  string		$seller            	Object of seller third party
	 *	@param  string  	$buyer             	Object of buyer third party
	 *	@param	int			$selected		   	Object line selected
	 *	@param  int	    	$dateSelector      	1=Show also date range input fields
	 *	@return	void
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
		//print '<td class="linecolht" align="right">'.$langs->trans('ICE').'</td>';
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
		$i	 = 0;

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
				//	$line->price = price2num($line->total_ttc / $line->qty,'MU');
				$this->printObjectLineadd($action,$line,$var,$num,$i,$dateSelector,$seller,$buyer,$selected,$extrafieldsline);
			}
			$i++;
		}
	}

	/**
	 *	Return HTML content of a detail line
	 *	TODO Move this into an output class file (htmlline.class.php)
	 *
	 *	@param	string		$action				GET/POST action
	 *	@param CommonObjectLine $line		       	Selected object line to output
	 *	@param  string	    $var               	Is it a an odd line (true)
	 *	@param  int		    $num               	Number of line (0)
	 *	@param  int		    $i					I
	 *	@param  int		    $dateSelector      	1=Show also date range input fields
	 *	@param  string	    $seller            	Object of seller third party
	 *	@param  string	    $buyer             	Object of buyer third party
	 *	@param	int			$selected		   	Object line selected
	 *  @param  int			$extrafieldsline	Object of extrafield line attribute
	 *	@return	void
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
					if (! empty($conf->global->PRODUIT_TEXTS_IN_THIRDPARTY_LANGUAGE) && empty($newlang)) $newlang=$this->thirdparty->default_lang;		// For language to language of customer
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
				$description.=(! empty($conf->global->PRODUIT_DESC_IN_FORM)?'':dol_htmlentitiesbr($line->description));	// Description is what to show on popup. We shown nothing if already into desc.
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
			$line->pu_ttc = price2num($line->price);
			$line->discount = price2num($line->remise);
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
	 *	Show add free and predefined products/services form
	 *
	 *  @param	int		        $dateSelector       1=Show also date range input fields
	 *  @param	Societe			$seller				Object thirdparty who sell
	 *  @param	Societe			$buyer				Object thirdparty who buy
	 *	@return	void
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
	 *	Add an order line into database (linked to product/service or not)
	 *
	 *	@param      string			$desc            	Description of line
	 *	@param      float			$pu_ht    	        Unit price (without tax)
	 *	@param      float			$qty             	Quantite
	 *	@param      float			$txtva           	Taux de tva force, sinon -1
	 *	@param      float			$txlocaltax1		Local tax 1 rate
	 *	@param      float			$txlocaltax2		Local tax 2 rate
	 *	@param      int				$fk_product      	Id of product
	 *	@param      float			$remise_percent  	Pourcentage de remise de la ligne
	 *	@param      int				$info_bits			Bits de type de lignes
	 *	@param      int				$fk_remise_except	Id remise
	 *	@param      string			$price_base_type	HT or TTC
	 *	@param      float			$pu_ttc    		    Prix unitaire TTC
	 *	@param      int				$date_start       	Start date of the line - Added by Matelli (See http://matelli.fr/showcases/patchs-dolibarr/add-dates-in-order-lines.html)
	 *	@param      int				$date_end         	End date of the line - Added by Matelli (See http://matelli.fr/showcases/patchs-dolibarr/add-dates-in-order-lines.html)
	 *	@param      int				$type				Type of line (0=product, 1=service). Not used if fk_product is defined, the type of product is used.
	 *	@param      int				$rang             	Position of line
	 *	@param		int				$special_code		Special code (also used by externals modules!)
	 *	@param		int				$fk_parent_line		Parent line
	 *  @param		int				$fk_fournprice		Id supplier price
	 *  @param		int				$pa_ht				Buying price (without tax)
	 *  @param		string			$label				Label
	 *  @param		array			$array_options		extrafields array. Example array('options_codeforfield1'=>'valueforfield1', 'options_codeforfield2'=>'valueforfield2', ...)
	 * 	@param 		string			$fk_unit 			Code of the unit to use. Null to use the default one
	 * 	@param		string		    $origin				'order', ...
	 *  @param		int			    $origin_id			Id of origin object
	 *	@return     int             					>0 if OK, <0 if KO
	 *
	 *	@see        add_product
	 *
	 *	Les parametres sont deja cense etre juste et avec valeurs finales a l'appel
	 *	de cette methode. Aussi, pour le taux tva, il doit deja avoir ete defini
	 *	par l'appelant par la methode get_default_tva(societe_vendeuse,societe_acheteuse,produit)
	 *	et le desc doit deja avoir la bonne valeur (a l'appelant de gerer le multilangue)
	 */
		function addlineadd($desc, $pu_ht, $qty, $txtva, $txlocaltax1=0, $txlocaltax2=0, $fk_product=0, $remise_percent=0, $info_bits=0, $fk_remise_except=0, $price_base_type='HT', $pu_ttc=0, $date_start='', $date_end='', $type=0, $rang=-1, $special_code=0, $fk_parent_line=0, $fk_fournprice=null, $pa_ht=0, $label='',$array_options=0, $fk_unit=null, $origin='', $origin_id=0,$lines)
		{
			global $mysoc, $conf, $langs;

			dol_syslog(get_class($this)."::addlineadd commandeid=$this->id, desc=$desc, pu_ht=$pu_ht, qty=$qty, txtva=$txtva, fk_product=$fk_product, remise_percent=$remise_percent, info_bits=$info_bits, fk_remise_except=$fk_remise_except, price_base_type=$price_base_type, pu_ttc=$pu_ttc, date_start=$date_start, date_end=$date_end, type=$type special_code=$special_code, fk_unit=$fk_unit", LOG_DEBUG);

			include_once DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php';

		// Clean parameters
			if (empty($remise_percent)) $remise_percent=0;
			if (empty($qty)) $qty=0;
			if (empty($info_bits)) $info_bits=0;
			if (empty($rang)) $rang=0;
			if (empty($txtva)) $txtva=0;
			if (empty($txlocaltax1)) $txlocaltax1=0;
			if (empty($txlocaltax2)) $txlocaltax2=0;
			if (empty($fk_parent_line) || $fk_parent_line < 0) $fk_parent_line=0;
			if (empty($this->fk_multicurrency)) $this->fk_multicurrency=0;

			$remise_percent=price2num($remise_percent);
			$qty=price2num($qty);
			$pu_ht=price2num($pu_ht);
			$pu_ttc=price2num($pu_ttc);
			$pa_ht=price2num($pa_ht);
			$txtva = price2num($txtva);
			$txlocaltax1 = price2num($txlocaltax1);
			$txlocaltax2 = price2num($txlocaltax2);
			if ($price_base_type=='HT')
			{
				$pu=$pu_ht;
			}
			else
			{
				$pu=$pu_ttc;
			}
			$label=trim($label);
			$desc=trim($desc);

		// Check parameters
			if ($type < 0) return -1;

			if ($this->statut == self::STATUS_DRAFT)
				{
					$this->db->begin();

					$product_type=$type;
					if (!empty($fk_product))
					{
						$product=new Product($this->db);
						$result=$product->fetch($fk_product);
						$product_type=$product->type;

						if (! empty($conf->global->STOCK_MUST_BE_ENOUGH_FOR_ORDER) && $product_type == 0 && $product->stock_reel < $qty)
						{
							$langs->load("errors");
							$this->error=$langs->trans('ErrorStockIsNotEnoughToAddProductOnOrder', $product->ref);
							dol_syslog(get_class($this)."::addline error=Product ".$product->ref.": ".$this->error, LOG_ERR);
							$this->db->rollback();
							return self::STOCK_NOT_ENOUGH_FOR_ORDER;
						}
					}
			// Calcul du total TTC et de la TVA pour la ligne a partir de
			// qty, pu, remise_percent et txtva
			// TRES IMPORTANT: C'est au moment de l'insertion ligne qu'on doit stocker
			// la part ht, tva et ttc, et ce au niveau de la ligne qui a son propre taux tva.

			//$localtaxes_type=getLocalTaxesFromRate($txtva,0,$this->thirdparty,$mysoc);
			//$txtva = preg_replace('/\s*\(.*\)/','',$txtva);  // Remove code into vatrate.

			//$tabprice = calcul_price_total($qty, $pu, $remise_percent, $txtva, $txlocaltax1, $txlocaltax2, 0, $price_base_type, $info_bits, $product_type, $mysoc, $localtaxes_type, 100, $this->multicurrency_tx);

					$total_ht  = $tabprice[0];
					$total_tva = $tabprice[1];
					$total_ttc = $tabprice[2];
					$total_localtax1 = $tabprice[9];
					$total_localtax2 = $tabprice[10];

					$total_ht  = $lines->total_ht;
					$total_tva = $lines->total_tva;
					$total_ttc = $lines->total_ttc;
					$total_localtax1 = $lines->total_localtax1;
					$total_localtax2 = $lines->total_localtax2;
					$remise = $lines->remise;
					$localtax1_type = $lines->localtax1_type;
					$localtax2_type = $lines->localtax2_type;

			// MultiCurrency
					$multicurrency_total_ht  = $tabprice[16];
					$multicurrency_total_tva = $tabprice[17];
					$multicurrency_total_ttc = $tabprice[18];

			// Rang to use
					$rangtouse = $rang;
					if ($rangtouse == -1)
					{
						$rangmax = $this->line_max($fk_parent_line);
						$rangtouse = $rangmax + 1;
					}

			// TODO A virer
			// Anciens indicateurs: $price, $remise (a ne plus utiliser)
					$price = $pu;
			//$remise = 0;
			//if ($remise_percent > 0)
			//{
			//    $remise = round(($pu * $remise_percent / 100), 2);
			//    $price = $pu - $remise;
			//}

			// Insert line
					$this->line=new OrderLineext($this->db);

					$this->line->context = $this->context;

					$this->line->fk_commande=$this->id;
					$this->line->label=$label;
					$this->line->desc=$desc;
					$this->line->qty=$qty;
					$this->line->tva_tx=$txtva;
					$this->line->localtax1_tx=$txlocaltax1;
					$this->line->localtax2_tx=$txlocaltax2;
					$this->line->localtax1_type = $localtax1_type;
					$this->line->localtax2_type = $localtax2_type;
					$this->line->fk_product=$fk_product;
					$this->line->product_type=$product_type;
					$this->line->fk_remise_except=$fk_remise_except;
					$this->line->remise_percent=$remise_percent;
					$this->line->remise=$remise;
					$this->line->subprice=$pu_ht;
					$this->line->price=$pu_ttc;
					$this->line->rang=$rangtouse;
					$this->line->info_bits=$info_bits;
					$this->line->total_ht=$total_ht;
					$this->line->total_tva=$total_tva;
					$this->line->total_localtax1=$total_localtax1;
					$this->line->total_localtax2=$total_localtax2;
					$this->line->total_ttc=$total_ttc;
					$this->line->product_type=$type;
					$this->line->special_code=$special_code;
					$this->line->origin=$origin;
					$this->line->origin_id=$origin_id;
					$this->line->fk_parent_line=$fk_parent_line;
					$this->line->fk_unit=$fk_unit;

					$this->line->date_start=$date_start;
					$this->line->date_end=$date_end;

					$this->line->fk_fournprice = $fk_fournprice;
					$this->line->pa_ht = $pa_ht;

			// Multicurrency
					$this->line->fk_multicurrency			= $this->fk_multicurrency;
					$this->line->multicurrency_code			= $this->multicurrency_code;
					$this->line->multicurrency_subprice		= price2num($pu_ht * $this->multicurrency_tx)+0;
					$this->line->multicurrency_total_ht 	= $multicurrency_total_ht+0;
					$this->line->multicurrency_total_tva 	= $multicurrency_total_tva+0;
					$this->line->multicurrency_total_ttc 	= $multicurrency_total_ttc+0;

			// TODO Ne plus utiliser
			//$this->line->price=$price;
					$this->line->remise=$remise;

					if (is_array($array_options) && count($array_options)>0) {
						$this->line->array_options=$array_options;
					}

					$result=$this->line->insertadd();
					if ($result > 0)
					{
				// Reorder if child line
						if (! empty($fk_parent_line)) $this->line_order(true,'DESC');

				// Mise a jour informations denormalisees au niveau de la commande meme
				//$result=$this->update_price(1,'auto');	// This method is designed to add line from user input so total calculation must be done using 'auto' mode.
						if ($result > 0)
						{
							$this->db->commit();
							return $this->line->rowid;
						}
						else
						{
							$this->db->rollback();
							return -1;
						}
					}
					else
					{
						$this->error=$this->line->error;
						dol_syslog(get_class($this)."::addline error=".$this->error, LOG_ERR);
						$this->db->rollback();
						return -2;
					}
				}
				else
				{
					dol_syslog(get_class($this)."::addline status of order must be Draft to allow use of ->addline()", LOG_ERR);
					return -3;
				}
			}

	/**
	 *  Update a line in database
	 *
	 *  @param    	int				$rowid            	Id of line to update
	 *  @param    	string			$desc             	Description de la ligne
	 *  @param    	float			$pu               	Prix unitaire
	 *  @param    	float			$qty              	Quantity
	 *  @param    	float			$remise_percent   	Pourcentage de remise de la ligne
	 *  @param    	float			$txtva           	Taux TVA
	 * 	@param		float			$txlocaltax1		Local tax 1 rate
	 *  @param		float			$txlocaltax2		Local tax 2 rate
	 *  @param    	string			$price_base_type	HT or TTC
	 *  @param    	int				$info_bits        	Miscellaneous informations on line
	 *  @param    	int		$date_start        	Start date of the line
	 *  @param    	int		$date_end          	End date of the line
	 * 	@param		int				$type				Type of line (0=product, 1=service)
	 * 	@param		int				$fk_parent_line		Id of parent line (0 in most cases, used by modules adding sublevels into lines).
	 * 	@param		int				$skip_update_total	Keep fields total_xxx to 0 (used for special lines by some modules)
	 *  @param		int				$fk_fournprice		Id of origin supplier price
	 *  @param		int				$pa_ht				Price (without tax) of product when it was bought
	 *  @param		string			$label				Label
	 *  @param		int				$special_code		Special code (also used by externals modules!)
	 *  @param		array			$array_options		extrafields array
	 * 	@param 		string			$fk_unit 			Code of the unit to use. Null to use the default one
	 *  @return   	int              					< 0 if KO, > 0 if OK
	 */
	function updatelineadd($rowid, $desc, $pu, $qty, $remise_percent, $txtva, $txlocaltax1=0.0,$txlocaltax2=0.0, $price_base_type='HT', $info_bits=0, $date_start='', $date_end='', $type=0, $fk_parent_line=0, $skip_update_total=0, $fk_fournprice=null, $pa_ht=0, $label='', $special_code=0, $array_options=0, $fk_unit=null,$lines)
	{
		global $conf, $mysoc, $langs;

		dol_syslog(get_class($this)."::updateline id=$rowid, desc=$desc, pu=$pu, qty=$qty, remise_percent=$remise_percent, txtva=$txtva, txlocaltax1=$txlocaltax1, txlocaltax2=$txlocaltax2, price_base_type=$price_base_type, info_bits=$info_bits, date_start=$date_start, date_end=$date_end, type=$type, fk_parent_line=$fk_parent_line, pa_ht=$pa_ht, special_code=$special_code");
		include_once DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php';

		if (! empty($this->brouillon))
		{
			$this->db->begin();
			// Clean parameters
			if (empty($qty)) $qty=0;
			if (empty($info_bits)) $info_bits=0;
			if (empty($txtva)) $txtva=0;
			if (empty($txlocaltax1)) $txlocaltax1=0;
			if (empty($txlocaltax2)) $txlocaltax2=0;
			if (empty($lines->remise)) $lines->remise=0;
			if (empty($remise_percent)) $remise_percent=0;
			if (empty($special_code) || $special_code == 3) $special_code=0;

			$remise_percent=price2num($remise_percent);
			$qty=price2num($qty);
			$pu = price2num($pu);
			$pu_ht = price2num($pu);
			$pa_ht=price2num($pa_ht);
			$txtva=price2num($txtva);
			$txlocaltax1=price2num($txlocaltax1);
			$txlocaltax2=price2num($txlocaltax2);

			// Calcul du total TTC et de la TVA pour la ligne a partir de
			// qty, pu, remise_percent et txtva
			// TRES IMPORTANT: C'est au moment de l'insertion ligne qu'on doit stocker
			// la part ht, tva et ttc, et ce au niveau de la ligne qui a son propre taux tva.

			//$localtaxes_type=getLocalTaxesFromRate($txtva,0,$this->thirdparty, $mysoc);
			//$txtva = preg_replace('/\s*\(.*\)/','',$txtva);  // Remove code into vatrate.

			//$tabprice=calcul_price_total($qty, $pu, $remise_percent, $txtva, $txlocaltax1, $txlocaltax2, 0, $price_base_type, $info_bits, $type, $mysoc, $localtaxes_type, 100, $this->multicurrency_tx);

			$total_ht  = $tabprice[0];
			$total_tva = $tabprice[1];
			$total_ttc = $tabprice[2];
			$total_localtax1 = $tabprice[9];
			$total_localtax2 = $tabprice[10];

			$total_ht  = price2num($lines->total_ht);
			$total_tva = price2num($lines->total_tva);
			$total_ttc = price2num($lines->total_ttc);
			$total_localtax1 = price2num($lines->total_localtax1);
			$total_localtax2 = price2num($lines->total_localtax2);
			$localtax1_type = $lines->localtax1_type;
			$localtax2_type = $lines->localtax2_type;
			$pu_ttc = $lines->price;
			// MultiCurrency
			$multicurrency_total_ht  = $tabprice[16];
			$multicurrency_total_tva = $tabprice[17];
			$multicurrency_total_ttc = $tabprice[18];

			// Anciens indicateurs: $price, $subprice, $remise (a ne plus utiliser)
			$price = $pu;
			if ($price_base_type == 'TTC')
			{
				$subprice = $tabprice[5];
			}
			else
			{
				$subprice = $pu;
			}
			//$remise = 0;
			//if ($remise_percent > 0)
			//{
			//    $remise = round(($pu * $remise_percent / 100),2);
			//    $price = ($pu - $remise);
			//}

			//Fetch current line from the database and then clone the object and set it in $oldline property
			$line = new OrderLineext($this->db);
			$line->fetch($rowid);

			if (!empty($line->fk_product))
			{
				$product=new Product($this->db);
				$result=$product->fetch($line->fk_product);
				$product_type=$product->type;

				if (! empty($conf->global->STOCK_MUST_BE_ENOUGH_FOR_ORDER) && $product_type == 0 && $product->stock_reel < $qty)
				{
					$langs->load("errors");
					$this->error=$langs->trans('ErrorStockIsNotEnoughToAddProductOnOrder', $product->ref);
					dol_syslog(get_class($this)."::addline error=Product ".$product->ref.": ".$this->error, LOG_ERR);
					$this->db->rollback();
					unset($_POST['productid']);
					unset($_POST['tva_tx']);
					unset($_POST['price_ht']);
					unset($_POST['qty']);
					unset($_POST['buying_price']);
					return self::STOCK_NOT_ENOUGH_FOR_ORDER;
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

			$this->line->rowid=$rowid;
			$this->line->label=$label;
			$this->line->desc=$desc;
			$this->line->qty=$qty;
			$this->line->tva_tx=$txtva;
			$this->line->localtax1_tx=$txlocaltax1;
			$this->line->localtax2_tx=$txlocaltax2;
			$this->line->localtax1_type = $localtax1_type;
			$this->line->localtax2_type = $localtax2_type;
			$this->line->remise_percent=$remise_percent;
			$this->line->subprice=$pu_ht;
			$this->line->info_bits=$info_bits;
			$this->line->special_code=$special_code;
			$this->line->total_ht=$total_ht;
			$this->line->total_tva=$total_tva;
			$this->line->total_localtax1=$total_localtax1+0;
			$this->line->total_localtax2=$total_localtax2+0;
			$this->line->total_ttc=$total_ttc;
			$this->line->date_start=$date_start;
			$this->line->date_end=$date_end;
			$this->line->product_type=$type;
			$this->line->fk_parent_line=$fk_parent_line;
			$this->line->skip_update_total=$skip_update_total;
			$this->line->fk_unit=$fk_unit;

			$this->line->fk_fournprice = $fk_fournprice;
			$this->line->pa_ht = $pa_ht;

			// Multicurrency
			$this->line->multicurrency_subprice		= price2num($subprice * $this->multicurrency_tx);
			$this->line->multicurrency_total_ht 	= $multicurrency_total_ht+0;
			$this->line->multicurrency_total_tva 	= $multicurrency_total_tva+0;
			$this->line->multicurrency_total_ttc 	= $multicurrency_total_ttc+0;

			// TODO deprecated
			$this->line->price=$pu_ttc;
			$this->line->remise=$lines->remise;

			if (is_array($array_options) && count($array_options)>0) {
				$this->line->array_options=$array_options;
			}
			$result=$this->line->updateadd();
			if ($result > 0)
			{
				// Reorder if child line
				if (! empty($fk_parent_line)) $this->line_order(true,'DESC');

				// Mise a jour info denormalisees
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
			$this->error=get_class($this)."::updatelineadd Order status makes operation forbidden";
			$this->errors=array('OrderStatusMakeOperationForbidden');
			return -2;
		}
	}

	function update_total()
	{
		global $conf,$user;

		$error=0;

		$pa_ht_isemptystring = (empty($this->pa_ht) && $this->pa_ht == ''); // If true, we can use a default value. If this->pa_ht = '0', we must use '0'.

		// Clean parameters
		if (empty($this->total_ht)) $this->total_ht=0;
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
		$sql = "UPDATE ".MAIN_DB_PREFIX."commande SET";
		$sql.= " tva='".price2num($this->tva)."'";
		$sql.= " , localtax1=".price2num($this->localtax1);
		$sql.= " , localtax2=".price2num($this->localtax2);
		$sql.= " , total_ht=".price2num($this->total_ht);
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
}

class OrderLineext extends OrderLine
{

	var $aData;
	function get_sum_taxes($id)
	{
		if (empty($id)) return -1;

		$sql = 'SELECT l.rowid, l.fk_product, l.fk_parent_line, l.product_type, l.fk_commande, l.label as custom_label, l.description, l.price, l.qty, l.tva_tx,';
		$sql.= ' l.localtax1_tx, l.localtax2_tx, l.fk_remise_except, l.remise_percent, l.subprice, l.fk_product_fournisseur_price as fk_fournprice, l.buy_price_ht as pa_ht, l.rang, l.info_bits, l.special_code,';
		$sql.= ' l.total_ht, l.total_ttc, l.total_tva, l.total_localtax1, l.total_localtax2, l.date_start, l.date_end,';
		$sql.= ' l.fk_unit,';
		$sql.= ' l.fk_multicurrency, l.multicurrency_code, l.multicurrency_subprice, l.multicurrency_total_ht, l.multicurrency_total_tva, l.multicurrency_total_ttc';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'commandedet as l';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON (p.rowid = l.fk_product)';
		$sql.= ' WHERE l.fk_commande = '.$id;
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
	 *	Insert line into database
	 *
	 *	@param      int		$notrigger		1 = disable triggers
	 *	@return		int						<0 if KO, >0 if OK
	 */
	function insertadd($notrigger=0)
	{
		global $langs, $conf, $user;

		$error=0;

		$pa_ht_isemptystring = (empty($this->pa_ht) && $this->pa_ht == ''); // If true, we can use a default value. If this->pa_ht = '0', we must use '0'.

		dol_syslog(get_class($this)."::insert rang=".$this->rang);

		// Clean parameters
		if (empty($this->tva_tx)) $this->tva_tx=0;
		if (empty($this->localtax1_tx)) $this->localtax1_tx=0;
		if (empty($this->localtax2_tx)) $this->localtax2_tx=0;
		if (empty($this->localtax1_type)) $this->localtax1_type=0;
		if (empty($this->localtax2_type)) $this->localtax2_type=0;
		if (empty($this->total_localtax1)) $this->total_localtax1=0;
		if (empty($this->total_localtax2)) $this->total_localtax2=0;
		if (empty($this->rang)) $this->rang=0;
		if (empty($this->remise)) $this->remise=0;
		if (empty($this->remise_percent)) $this->remise_percent=0;
		if (empty($this->info_bits)) $this->info_bits=0;
		if (empty($this->special_code)) $this->special_code=0;
		if (empty($this->fk_parent_line)) $this->fk_parent_line=0;
		if (empty($this->pa_ht)) $this->pa_ht=0;
		if (empty($this->origin_id)) $this->origin_id=0;

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
		if ($this->product_type < 0) return -1;

		$this->db->begin();

		// Insertion dans base de la ligne
		$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'commandedet';
		$sql.= ' (fk_commande, fk_parent_line, label, description, qty, ';
		$sql.= ' tva_tx, localtax1_tx, localtax2_tx, localtax1_type, localtax2_type,';
		$sql.= ' fk_product, product_type, remise_percent, subprice, price, remise, fk_remise_except,';
		$sql.= ' special_code, rang, fk_product_fournisseur_price, buy_price_ht,';
		$sql.= ' info_bits, total_ht, total_tva, total_localtax1, total_localtax2, total_ttc, date_start, date_end,';
		$sql.= ' fk_unit, origin, origin_id';
		$sql.= ', fk_multicurrency, multicurrency_code, multicurrency_subprice, multicurrency_total_ht, multicurrency_total_tva, multicurrency_total_ttc';
		$sql.= ')';
		$sql.= " VALUES (".$this->fk_commande.",";
		$sql.= " ".($this->fk_parent_line>0?"'".$this->fk_parent_line."'":"null").",";
		$sql.= " ".(! empty($this->label)?"'".$this->db->escape($this->label)."'":"null").",";
		$sql.= " '".$this->db->escape($this->desc)."',";
		$sql.= " '".price2num($this->qty)."',";
		$sql.= " '".price2num($this->tva_tx)."',";
		$sql.= " '".price2num($this->localtax1_tx)."',";
		$sql.= " '".price2num($this->localtax2_tx)."',";
		$sql.= " '".$this->localtax1_type."',";
		$sql.= " '".$this->localtax2_type."',";
		$sql.= ' '.(! empty($this->fk_product)?$this->fk_product:"null").',';
		$sql.= " '".$this->product_type."',";
		$sql.= " '".price2num($this->remise_percent)."',";
		$sql.= " ".($this->subprice!=''?"'".price2num($this->subprice)."'":"null").",";
		$sql.= " ".($this->price!=''?"'".price2num($this->price)."'":"null").",";
		$sql.= " '".price2num($this->remise)."',";
		$sql.= ' '.(! empty($this->fk_remise_except)?$this->fk_remise_except:"null").',';
		$sql.= ' '.$this->special_code.',';
		$sql.= ' '.$this->rang.',';
		$sql.= ' '.(! empty($this->fk_fournprice)?$this->fk_fournprice:"null").',';
		$sql.= ' '.price2num($this->pa_ht).',';
		$sql.= " '".$this->info_bits."',";
		$sql.= " '".price2num($this->total_ht)."',";
		$sql.= " '".price2num($this->total_tva)."',";
		$sql.= " '".price2num($this->total_localtax1)."',";
		$sql.= " '".price2num($this->total_localtax2)."',";
		$sql.= " '".price2num($this->total_ttc)."',";
		$sql.= " ".(! empty($this->date_start)?"'".$this->db->idate($this->date_start)."'":"null").',';
		$sql.= " ".(! empty($this->date_end)?"'".$this->db->idate($this->date_end)."'":"null").',';
		$sql.= ' '.(!$this->fk_unit ? 'NULL' : $this->fk_unit);
		$sql.= ", '".$this->origin."',";
		$sql.= " ".$this->origin_id;
		$sql.= ", ".$this->fk_multicurrency;
		$sql.= ", '".$this->db->escape($this->multicurrency_code)."'";
		$sql.= ", ".$this->multicurrency_subprice;
		$sql.= ", ".$this->multicurrency_total_ht;
		$sql.= ", ".$this->multicurrency_total_tva;
		$sql.= ", ".$this->multicurrency_total_ttc;
		$sql.= ')';
		dol_syslog(get_class($this)."::insertadd", LOG_DEBUG);

		$resql=$this->db->query($sql);
		if ($resql)
		{
			$this->rowid=$this->db->last_insert_id(MAIN_DB_PREFIX.'commandedet');

			if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED)) // For avoid conflicts if trigger used
			{
				$this->id=$this->rowid;
				$result=$this->insertExtraFields();
				if ($result < 0)
				{
					$error++;
				}
			}

			if (! $error && ! $notrigger)
			{
				// Call trigger
				$result=$this->call_trigger('LINEORDER_INSERT',$user);
				if ($result < 0) $error++;
				// End call triggers
			}

			if (!$error) {
				$this->db->commit();
				return 1;
			}

			foreach($this->errors as $errmsg)
			{
				dol_syslog(get_class($this)."::delete ".$errmsg, LOG_ERR);
				$this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->error=$this->db->error();
			$this->db->rollback();
			return -2;
		}
	}

	/**
	 *	Update the line object into db
	 *
	 *	@param      int		$notrigger		1 = disable triggers
	 *	@return		int		<0 si ko, >0 si ok
	 */
	function updateadd($notrigger=0)
	{
		global $conf,$langs,$user;

		$error=0;

		$pa_ht_isemptystring = (empty($this->pa_ht) && $this->pa_ht == ''); // If true, we can use a default value. If this->pa_ht = '0', we must use '0'.

		// Clean parameters
		if (empty($this->tva_tx)) $this->tva_tx=0;
		if (empty($this->localtax1_tx)) $this->localtax1_tx=0;
		if (empty($this->localtax2_tx)) $this->localtax2_tx=0;
		if (empty($this->localtax1_type)) $this->localtax1_type=0;
		if (empty($this->localtax2_type)) $this->localtax2_type=0;
		if (empty($this->qty)) $this->qty=0;
		if (empty($this->total_localtax1)) $this->total_localtax1=0;
		if (empty($this->total_localtax2)) $this->total_localtax2=0;
		if (empty($this->marque_tx)) $this->marque_tx=0;
		if (empty($this->marge_tx)) $this->marge_tx=0;
		if (empty($this->remise)) $this->remise=0;
		if (empty($this->remise_percent)) $this->remise_percent=0;
		if (empty($this->info_bits)) $this->info_bits=0;
		if (empty($this->special_code)) $this->special_code=0;
		if (empty($this->product_type)) $this->product_type=0;
		if (empty($this->fk_parent_line)) $this->fk_parent_line=0;
		if (empty($this->pa_ht)) $this->pa_ht=0;

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
		$sql = "UPDATE ".MAIN_DB_PREFIX."commandedet SET";
		$sql.= " description='".$this->db->escape($this->desc)."'";
		$sql.= " , label=".(! empty($this->label)?"'".$this->db->escape($this->label)."'":"null");
		$sql.= " , tva_tx=".price2num($this->tva_tx);
		$sql.= " , localtax1_tx=".price2num($this->localtax1_tx);
		$sql.= " , localtax2_tx=".price2num($this->localtax2_tx);
		$sql.= " , localtax1_type='".$this->localtax1_type."'";
		$sql.= " , localtax2_type='".$this->localtax2_type."'";
		$sql.= " , qty=".price2num($this->qty);
		$sql.= " , subprice=".price2num($this->subprice)."";
		$sql.= " , remise_percent=".price2num($this->remise_percent)."";
		$sql.= " , price=".price2num($this->price)."";					// TODO A virer
		$sql.= " , remise=".price2num($this->remise)."";				// TODO A virer

		if (empty($this->skip_update_total))
		{
			$sql.= " , total_ht=".price2num($this->total_ht)."";
			$sql.= " , total_tva=".price2num($this->total_tva)."";
			$sql.= " , total_ttc=".price2num($this->total_ttc)."";
			$sql.= " , total_localtax1=".price2num($this->total_localtax1);
			$sql.= " , total_localtax2=".price2num($this->total_localtax2);
		}
		$sql.= " , fk_product_fournisseur_price=".(! empty($this->fk_fournprice)?$this->fk_fournprice:"null");
		$sql.= " , buy_price_ht='".price2num($this->pa_ht)."'";
		$sql.= " , info_bits=".$this->info_bits;
		$sql.= " , special_code=".$this->special_code;
		$sql.= " , date_start=".(! empty($this->date_start)?"'".$this->db->idate($this->date_start)."'":"null");
		$sql.= " , date_end=".(! empty($this->date_end)?"'".$this->db->idate($this->date_end)."'":"null");
		$sql.= " , product_type=".$this->product_type;
		$sql.= " , fk_parent_line=".(! empty($this->fk_parent_line)?$this->fk_parent_line:"null");
		if (! empty($this->rang)) $sql.= ", rang=".$this->rang;
		$sql.= " , fk_unit=".(!$this->fk_unit ? 'NULL' : $this->fk_unit);

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
				$result=$this->call_trigger('LINEORDER_UPDATE',$user);
				if ($result < 0) $error++;
				// End call triggers
			}

			if (!$error) {
				$this->db->commit();
				return 1;
			}

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
			$this->error=$this->db->error();
			$this->db->rollback();
			return -2;
		}
	}
}
?>