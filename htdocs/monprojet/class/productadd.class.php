<?php
require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';

class Productadd extends Product
{
	var $nbtotalofrecords;
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false,$group='')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);
		global $conf;

		$sql = "SELECT t.rowid, t.ref, t.ref_ext, t.label, t.description, t.url, t.note, t.customcode, t.fk_country, t.price, t.price_ttc,";
		$sql.= " t.price_min, t.price_min_ttc, t.price_base_type, t.cost_price, t.default_vat_code, t.tva_tx, t.recuperableonly as tva_npr, t.localtax1_tx, t.localtax2_tx, t.localtax1_type, t.localtax2_type, t.tosell,";
		$sql.= " t.tobuy, t.fk_product_type, t.duration, t.seuil_stock_alerte, t.canvas,";
		$sql.= " t.weight, t.weight_units, t.length, t.length_units, t.surface, t.surface_units, t.volume, t.volume_units, t.barcode, t.fk_barcode_type, t.finished,";
		$sql.= " t.accountancy_code_buy, t.accountancy_code_sell, t.stock, t.pmp,";
		$sql.= " t.datec, t.tms, t.import_key, t.entity, t.desiredstock, t.tobatch, t.fk_unit,";
		$sql.= " t.fk_price_expression, t.price_autogen";
		if ($group) $sql.= ", c.fk_categorie ";
		$sql.= " FROM ".MAIN_DB_PREFIX."product AS t";
		if ($group)
		{
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."categorie_product AS c ON c.fk_product = t.rowid ";
		}
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
		if ($group) $sql.= $group;

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield,$sortorder);
		}
		if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST))
		{
			$result = $this->db->query($sql);
			$this->nbtotalofrecords = $this->db->num_rows($result);
		}
		if (!empty($limit)) {
			$sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}

		$this->lines = array();
		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) 
			{

				$line = new ProductLine();

				$line->id						= $obj->rowid;
				$line->ref						= $obj->ref;
				$line->ref_ext					= $obj->ref_ext;
				$line->label					= $obj->label;
				$line->description				= $obj->description;
				$line->url						= $obj->url;
				$line->note						= $obj->note;

				$line->type						= $obj->fk_product_type;
				$line->fk_categorie				= $obj->fk_categorie;
				$line->status					= $obj->tosell;
				$line->status_buy				= $obj->tobuy;
				$line->status_batch				= $obj->tobatch;

				$line->customcode				= $obj->customcode;
				$line->country_id				= $obj->fk_country;
				$line->price					= $obj->price;
				$line->price_ttc				= $obj->price_ttc;
				$line->price_min				= $obj->price_min;
				$line->price_min_ttc			= $obj->price_min_ttc;
				$line->price_base_type			= $obj->price_base_type;
				$line->cost_price    			= $obj->cost_price;
				$line->default_vat_code 		= $obj->default_vat_code;
				$line->tva_tx					= $obj->tva_tx;
				//! French VAT NPR
				$line->tva_npr					= $obj->tva_npr;
				//! Local taxes
				$line->localtax1_tx				= $obj->localtax1_tx;
				$line->localtax2_tx				= $obj->localtax2_tx;
				$line->localtax1_type			= $obj->localtax1_type;
				$line->localtax2_type			= $obj->localtax2_type;
				
				$line->finished					= $obj->finished;
				$line->duration					= $obj->duration;
				$line->duration_value			= substr($obj->duration,0,dol_strlen($obj->duration)-1);
				$line->duration_unit			= substr($obj->duration,-1);
				$line->canvas					= $obj->canvas;
				$line->weight					= $obj->weight;
				$line->weight_units				= $obj->weight_units;
				$line->length					= $obj->length;
				$line->length_units				= $obj->length_units;
				$line->surface					= $obj->surface;
				$line->surface_units			= $obj->surface_units;
				$line->volume					= $obj->volume;
				$line->volume_units				= $obj->volume_units;
				$line->barcode					= $obj->barcode;
				$line->barcode_type				= $obj->fk_barcode_type;

				$line->accountancy_code_buy		= $obj->accountancy_code_buy;
				$line->accountancy_code_sell	= $obj->accountancy_code_sell;

				$line->seuil_stock_alerte		= $obj->seuil_stock_alerte;
				$line->desiredstock             = $obj->desiredstock;
				$line->stock_reel				= $obj->stock;
				$line->pmp						= $obj->pmp;

				$line->date_creation			= $obj->datec;
				$line->date_modification		= $obj->tms;
				$line->import_key				= $obj->import_key;
				$line->entity					= $obj->entity;

				$line->ref_ext					= $obj->ref_ext;
				$line->fk_price_expression		= $obj->fk_price_expression;
				$line->fk_unit					= $obj->fk_unit;
				$line->price_autogen			= $obj->price_autogen;

// Retreive all extrafield for current object
				// fetch optionals attributes and labels
				require_once(DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php');
				$extrafields=new ExtraFields($this->db);
				$extralabels=$extrafields->fetch_name_optionals_label($this->table_element,true);
				$this->fetch_optionals($this->id,$extralabels);


				// multilangs
				if (! empty($conf->global->MAIN_MULTILANGS)) $this->getMultiLangs();

				// Load multiprices array
				if (! empty($conf->global->PRODUIT_MULTIPRICES))
				{
					for ($i=1; $i <= $conf->global->PRODUIT_MULTIPRICES_LIMIT; $i++)
					{
						$sql = "SELECT price, price_ttc, price_min, price_min_ttc,";
						$sql.= " price_base_type, tva_tx, tosell, price_by_qty, rowid, recuperableonly";
						$sql.= " FROM ".MAIN_DB_PREFIX."product_price";
						$sql.= " WHERE entity IN (".getEntity('productprice', 1).")";
						$sql.= " AND price_level=".$i;
						$sql.= " AND fk_product = '".$this->id."'";
						$sql.= " ORDER BY date_price DESC, rowid DESC";
						$sql.= " LIMIT 1";
						$resql = $this->db->query($sql);
						if ($resql)
						{
							$result = $this->db->fetch_array($resql);

							$line->multiprices[$i]=$result["price"];
							$line->multiprices_ttc[$i]=$result["price_ttc"];
							$line->multiprices_min[$i]=$result["price_min"];
							$line->multiprices_min_ttc[$i]=$result["price_min_ttc"];
							$line->multiprices_base_type[$i]=$result["price_base_type"];
							$line->multiprices_tva_tx[$i]=$result["tva_tx"];
							$line->multiprices_recuperableonly[$i]=$result["recuperableonly"];

							// Price by quantity
							$line->prices_by_qty[$i]=$result["price_by_qty"];
							$line->prices_by_qty_id[$i]=$result["rowid"];
							// Récuperation de la liste des prix selon qty si flag positionné
							if ($line->prices_by_qty[$i] == 1)
							{
								$sql = "SELECT rowid,price, unitprice, quantity, remise_percent, remise";
								$sql.= " FROM ".MAIN_DB_PREFIX."product_price_by_qty";
								$sql.= " WHERE fk_product_price = '".$this->prices_by_qty_id[$i]."'";
								$sql.= " ORDER BY quantity ASC";
								$resultat=array();
								$resql = $this->db->query($sql);
								if ($resql)
								{
									$ii=0;
									while ($result= $this->db->fetch_array($resql)) {
										$resultat[$ii]=array();
										$resultat[$ii]["rowid"]=$result["rowid"];
										$resultat[$ii]["price"]= $result["price"];
										$resultat[$ii]["unitprice"]= $result["unitprice"];
										$resultat[$ii]["quantity"]= $result["quantity"];
										$resultat[$ii]["remise_percent"]= $result["remise_percent"];
										$resultat[$ii]["remise"]= $result["remise"];
										$ii++;
									}
									$line->prices_by_qty_list[$i]=$resultat;
								}
								else
								{
									dol_print_error($this->db);
									return -1;
								}
							}
						}
						else
						{
							dol_print_error($this->db);
							return -1;
						}
					}
				} else if (! empty($conf->global->PRODUIT_CUSTOMER_PRICES_BY_QTY))
				{
					$sql = "SELECT price, price_ttc, price_min, price_min_ttc,";
					$sql.= " price_base_type, tva_tx, tosell, price_by_qty, rowid";
					$sql.= " FROM ".MAIN_DB_PREFIX."product_price";
					$sql.= " WHERE fk_product = '".$this->id."'";
					$sql.= " ORDER BY date_price DESC, rowid DESC";
					$sql.= " LIMIT 1";
					$resql = $this->db->query($sql);
					if ($resql)
					{
						$result = $this->db->fetch_array($resql);

						// Price by quantity
						$line->prices_by_qty[0]=$result["price_by_qty"];
						$line->prices_by_qty_id[0]=$result["rowid"];
						// Récuperation de la liste des prix selon qty si flag positionné
						if ($line->prices_by_qty[0] == 1)
						{
							$sql = "SELECT rowid,price, unitprice, quantity, remise_percent, remise";
							$sql.= " FROM ".MAIN_DB_PREFIX."product_price_by_qty";
							$sql.= " WHERE fk_product_price = '".$this->prices_by_qty_id[0]."'";
							$sql.= " ORDER BY quantity ASC";
							$resultat=array();
							$resql = $this->db->query($sql);
							if ($resql)
							{
								$ii=0;
								while ($result= $this->db->fetch_array($resql)) {
									$resultat[$ii]=array();
									$resultat[$ii]["rowid"]=$result["rowid"];
									$resultat[$ii]["price"]= $result["price"];
									$resultat[$ii]["unitprice"]= $result["unitprice"];
									$resultat[$ii]["quantity"]= $result["quantity"];
									$resultat[$ii]["remise_percent"]= $result["remise_percent"];
									$resultat[$ii]["remise"]= $result["remise"];
									$ii++;
								}
								$line->prices_by_qty_list[0]=$resultat;
							}
							else
							{
								dol_print_error($this->db);
								return -1;
							}
						}
					}
					else
					{
						dol_print_error($this->db);
						return -1;
					}
				}

                if (!empty($conf->dynamicprices->enabled) && !empty($this->fk_price_expression) && empty($ignore_expression))
                {
					require_once DOL_DOCUMENT_ROOT.'/product/dynamic_price/class/price_parser.class.php';
                	$priceparser = new PriceParser($this->db);
                    $price_result = $priceparser->parseProduct($this);
                    if ($price_result >= 0)
                    {
                        $line->price = $price_result;
                        //Calculate the VAT
						$line->price_ttc = price2num($this->price) * (1 + ($this->tva_tx / 100));
						$line->price_ttc = price2num($this->price_ttc,'MU');
                    }
                }

				if ($lView)
				{
					$this->id						= $obj->rowid;
					$this->ref						= $obj->ref;
					$this->ref_ext					= $obj->ref_ext;
					$this->label					= $obj->label;
					$this->description				= $obj->description;
					$this->url						= $obj->url;
					$this->note						= $obj->note;

					$this->type						= $obj->fk_product_type;
					$this->status					= $obj->tosell;
					$this->status_buy				= $obj->tobuy;
					$this->status_batch				= $obj->tobatch;

					$this->customcode				= $obj->customcode;
					$this->country_id				= $obj->fk_country;
					$this->country_code				= getCountry($this->country_id,2,$this->db);
					$this->price					= $obj->price;
					$this->price_ttc				= $obj->price_ttc;
					$this->price_min				= $obj->price_min;
					$this->price_min_ttc			= $obj->price_min_ttc;
					$this->price_base_type			= $obj->price_base_type;
					$this->cost_price    			= $obj->cost_price;
					$this->default_vat_code 		= $obj->default_vat_code;
					$this->tva_tx					= $obj->tva_tx;
				//! French VAT NPR
					$this->tva_npr					= $obj->tva_npr;
				//! Local taxes
					$this->localtax1_tx				= $obj->localtax1_tx;
					$this->localtax2_tx				= $obj->localtax2_tx;
					$this->localtax1_type			= $obj->localtax1_type;
					$this->localtax2_type			= $obj->localtax2_type;

					$this->finished					= $obj->finished;
					$this->duration					= $obj->duration;
					$this->duration_value			= substr($obj->duration,0,dol_strlen($obj->duration)-1);
					$this->duration_unit			= substr($obj->duration,-1);
					$this->canvas					= $obj->canvas;
					$this->weight					= $obj->weight;
					$this->weight_units				= $obj->weight_units;
					$this->length					= $obj->length;
					$this->length_units				= $obj->length_units;
					$this->surface					= $obj->surface;
					$this->surface_units			= $obj->surface_units;
					$this->volume					= $obj->volume;
					$this->volume_units				= $obj->volume_units;
					$this->barcode					= $obj->barcode;
					$this->barcode_type				= $obj->fk_barcode_type;

					$this->accountancy_code_buy		= $obj->accountancy_code_buy;
					$this->accountancy_code_sell	= $obj->accountancy_code_sell;

					$this->seuil_stock_alerte		= $obj->seuil_stock_alerte;
					$this->desiredstock             = $obj->desiredstock;
					$this->stock_reel				= $obj->stock;
					$this->pmp						= $obj->pmp;

					$this->date_creation			= $obj->datec;
					$this->date_modification		= $obj->tms;
					$this->import_key				= $obj->import_key;
					$this->entity					= $obj->entity;

					$this->ref_ext					= $obj->ref_ext;
					$this->fk_price_expression		= $obj->fk_price_expression;
					$this->fk_unit					= $obj->fk_unit;
					$this->price_autogen			= $obj->price_autogen;
				}

				$this->lines[] = $line;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}

}

class ProductLine
{
	/*
	 * @deprecated
	 * @see label
	 */
	var $libelle;
	/**
	 * Product label
	 * @var string
	 */
	var $label;
    /**
     * Product descripion
     * @var string
     */
    var $description;

	/**
	 * Check TYPE constants
	 * @var int
	 */
	var $type;
	//! Selling price
	var $price;				// Price net
	var $price_ttc;			// Price with tax
	var $price_min;         // Minimum price net
	var $price_min_ttc;     // Minimum price with tax
	//! Base price ('TTC' for price including tax or 'HT' for net price)
	var $price_base_type;
	//! Arrays for multiprices
	public $multiprices=array();
	public $multiprices_ttc=array();
	public $multiprices_base_type=array();
	public $multiprices_min=array();
	public $multiprices_min_ttc=array();
	public $multiprices_tva_tx=array();
	public $multiprices_recuperableonly=array();
	//! Price by quantity arrays
	var $price_by_qty;
	var $prices_by_qty=array();
	var $prices_by_qty_id=array();
	var $prices_by_qty_list=array();
	//! Default VAT code for product (link to code into llx_c_tva but without foreign keys)
	var $default_vat_code;
	//! Default VAT rate of product
	var $tva_tx;
	//! French VAT NPR (0 or 1)
	var $tva_npr=0;
	//! Other local taxes
	var $localtax1_tx;
	var $localtax2_tx;
	var $localtax1_type;
	var $localtax2_type;
	
	//! Stock real
	var $stock_reel;
	//! Stock virtual
	var $stock_theorique;
	//! Cost price
	var $cost_price;
	//! Average price value for product entry into stock (PMP)
	var $pmp;
    //! Stock alert
	var $seuil_stock_alerte;
	//! Ask for replenishment when $desiredstock < $stock_reel
	public $desiredstock;
	//! Duree de validite du service
	var $duration_value;
	//! Unite de duree
	var $duration_unit;
	// Statut indique si le produit est en vente '1' ou non '0'
	var $status;
	// Status indicate whether the product is available for purchase '1' or not '0'
	var $status_buy;
	// Statut indique si le produit est un produit fini '1' ou une matiere premiere '0'
	var $finished;
	// We must manage lot/batch number, sell-by date and so on : '1':yes '0':no
	var $status_batch;

	var $customcode;       // Customs code

	/**
	 * Product URL
	 * @var string
	 */
	public $url;

	//! Unites de mesure
	var $weight;
	var $weight_units;
	var $length;
	var $length_units;
	var $surface;
	var $surface_units;
	var $volume;
	var $volume_units;

	var $accountancy_code_buy;
	var $accountancy_code_sell;

	//! barcode
	var $barcode;               // value

	var $stats_propale=array();
	var $stats_commande=array();
	var $stats_contrat=array();
	var $stats_facture=array();
	var $stats_commande_fournisseur=array();

	var $multilangs=array();

	//! Taille de l'image
	var $imgWidth;
	var $imgHeight;

	var $date_creation;
	var $date_modification;

	//! Id du fournisseur
	var $product_fourn_id;

	//! Product ID already linked to a reference supplier
	var $product_id_already_linked;

	var $nbphoto;

	//! Contains detail of stock of product into each warehouse
	var $stock_warehouse=array();

	var $oldcopy;

	var $fk_price_expression;

	/**
	 * @deprecated
	 * @see fourn_pu
	 */
	var $buyprice;
	public $fourn_pu;

	/**
	 * @deprecated
	 * @see ref_supplier
	 */
	var $ref_fourn;
	public $ref_supplier;

	/**
	 * Unit code ('km', 'm', 'l', 'p', ...)
	 * @var string
	 */
	public $fk_unit;

	/**
	 * Price is generated using multiprice rules
	 * @var int
	 */
	public $price_autogen = 0;

}
?>