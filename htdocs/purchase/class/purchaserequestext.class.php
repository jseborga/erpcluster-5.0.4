<?php
require_once DOL_DOCUMENT_ROOT.'/purchase/class/purchaserequest.class.php';

class Purchaserequestext extends Purchaserequest
{
		/**
	 * Draft status
	 */
		const STATUS_DRAFT = 0;
	/**
	 * Validated status
	 */
	const STATUS_VALIDATED = 1;
	/**
	 * Signed quote
	 */
	const STATUS_SIGNED = 2;
	/**
	 * Not signed quote
	 */
	const STATUS_NOTSIGNED = 3;
	/**
	 * Billed or processed quote
	 */
	const STATUS_BILLED = 4;
	var $unit;

	/**
	 *  Return a link to the object card (with optionaly the picto)
	 *
	 *	@param	int		$withpicto			Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *	@param	string	$option				On what the link point to
	 *  @param	int  	$notooltip			1=Disable tooltip
	 *  @param	int		$maxlen				Max length of visible user name
	 *  @param  string  $morecss            Add more css on link
	 *	@return	string						String with URL
	 */
	function getNomUrladd($withpicto=0, $option='', $notooltip=0, $maxlen=24, $morecss='')
	{
		global $db, $conf, $langs;
		global $dolibarr_main_authentication, $dolibarr_main_demo;
		global $menumanager;

		if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

		$result = '';
		$companylink = '';

		$label = '<u>' . $langs->trans("Purchaserequest") . '</u>';
		$label.= '<br>';
		$label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

		$url = DOL_URL_ROOT.'/purchase/request/'.'card.php?id='.$this->id;

		$linkclose='';
		if (empty($notooltip))
		{
			if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
			{
				$label=$langs->trans("ShowProject");
				$linkclose.=' alt="'.dol_escape_htmltag($label, 1).'"';
			}
			$linkclose.=' title="'.dol_escape_htmltag($label, 1).'"';
			$linkclose.=' class="classfortooltip'.($morecss?' '.$morecss:'').'"';
		}
		else $linkclose = ($morecss?' class="'.$morecss.'"':'');

		$linkstart = '<a href="'.$url.'"';
		$linkstart.=$linkclose.'>';
		$linkend='</a>';

		if ($withpicto)
		{
			$result.=($linkstart.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
			if ($withpicto != 2) $result.=' ';
		}
		$result.= $linkstart . $this->ref . $linkend;
		return $result;
	}

	/**
	 * 	Add line into array products
	 *	$this->client doit etre charge
	 *
	 * 	@param  int		$idproduct       	Product Id to add
	 * 	@param  int		$qty             	Quantity
	 * 	@param  int		$remise_percent  	Discount effected on Product
	 *  @return	int							<0 if KO, >0 if OK
	 *
	 *	TODO	Remplacer les appels a cette fonction par generation objet Ligne
	 *			insere dans tableau $this->products
	 */
	function add_product($idproduct, $qty, $remise_percent=0)
	{
		global $conf, $mysoc;

		if (! $qty) $qty = 1;

		dol_syslog(get_class($this)."::add_product $idproduct, $qty, $remise_percent");
		if ($idproduct > 0)
		{
			$prod=new Product($this->db);
			$prod->fetch($idproduct);

			$productdesc = $prod->description;

			$tva_tx = get_default_tva($mysoc,$this->thirdparty,$prod->id);
			$tva_npr = get_default_npr($mysoc,$this->thirdparty,$prod->id);
			if (empty($tva_tx)) $tva_npr=0;
			$localtax1_tx = get_localtax($tva_tx,1,$mysoc,$this->thirdparty,$tva_npr);
			$localtax2_tx = get_localtax($tva_tx,2,$mysoc,$this->thirdparty,$tva_npr);

			// multiprix
			if($conf->global->PRODUIT_MULTIPRICES && $this->thirdparty->price_level)
			{
				$price = $prod->multiprices[$this->thirdparty->price_level];
			}
			else
			{
				$price = $prod->price;
			}

			$line = new PurchaserequestLine($this->db);

			$line->fk_product=$idproduct;
			$line->desc=$productdesc;
			$line->qty=$qty;
			$line->subprice=$price;
			$line->remise_percent=$remise_percent;
			$line->tva_tx=$tva_tx;

			$this->lines[]=$line;
		}
	}

	/**
	 *	Adding line of fixed discount in the proposal in DB
	 *
	 *	@param     int		$idremise			Id of fixed discount
	 *  @return    int          				>0 if OK, <0 if KO
	 */
	function insert_discount($idremise)
	{
		global $langs;

		include_once DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php';
		include_once DOL_DOCUMENT_ROOT.'/core/class/discount.class.php';

		$this->db->begin();

		$remise=new DiscountAbsolute($this->db);
		$result=$remise->fetch($idremise);

		if ($result > 0)
		{
			if ($remise->fk_facture)	// Protection against multiple submission
			{
				$this->error=$langs->trans("ErrorDiscountAlreadyUsed");
				$this->db->rollback();
				return -5;
			}

			$purchase_requestline=new PurchaserequestLine($this->db);
			$purchase_requestline->fk_purchase_request=$this->id;
			$purchase_requestline->fk_remise_except=$remise->id;
			$purchase_requestline->desc=$remise->description;   	// Description ligne
			$purchase_requestline->tva_tx=$remise->tva_tx;
			$purchase_requestline->subprice=-$remise->amount_ht;
			$purchase_requestline->fk_product=0;					// Id produit predefini
			$purchase_requestline->qty=1;
			$purchase_requestline->remise=0;
			$purchase_requestline->remise_percent=0;
			$purchase_requestline->rang=-1;
			$purchase_requestline->info_bits=2;

			// TODO deprecated
			$purchase_requestline->price=-$remise->amount_ht;

			$purchase_requestline->total_ht  = -$remise->amount_ht;
			$purchase_requestline->total_tva = -$remise->amount_tva;
			$purchase_requestline->total_ttc = -$remise->amount_ttc;

			$result=$purchase_requestline->insert();
			if ($result > 0)
			{
				$result=$this->update_price(1);
				if ($result > 0)
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
			else
			{
				$this->error=$purchase_requestline->error;
				$this->db->rollback();
				return -2;
			}
		}
		else
		{
			$this->db->rollback();
			return -2;
		}
	}

	/**
	 *    	Add a proposal line into database (linked to product/service or not)
	 * 		Les parametres sont deja cense etre juste et avec valeurs finales a l'appel
	 *		de cette methode. Aussi, pour le taux tva, il doit deja avoir ete defini
	 *		par l'appelant par la methode get_default_tva(societe_vendeuse,societe_acheteuse,'',produit)
	 *		et le desc doit deja avoir la bonne valeur (a l'appelant de gerer le multilangue)
	 *
	 * 		@param    	string		$desc				Description de la ligne
	 * 		@param    	double		$pu_ht				Prix unitaire
	 * 		@param    	double		$qty             	Quantite
	 * 		@param    	double		$txtva           	Taux de tva
	 * 		@param		double		$txlocaltax1		Local tax 1 rate
	 *  	@param		double		$txlocaltax2		Local tax 2 rate
	 *		@param    	int			$fk_product      	Id du produit/service predefini
	 * 		@param    	double		$remise_percent  	Pourcentage de remise de la ligne
	 * 		@param    	string		$price_base_type	HT or TTC
	 * 		@param    	double		$pu_ttc             Prix unitaire TTC
	 * 		@param    	int			$info_bits			Bits de type de lignes
	 *      @param      int			$type               Type of line (product, service)
	 *      @param      int			$rang               Position of line
	 *      @param		int			$special_code		Special code (also used by externals modules!)
	 *      @param		int			$fk_parent_line		Id of parent line
	 *      @param		int			$fk_fournprice		Id supplier price
	 *      @param		int			$pa_ht				Buying price without tax
	 *      @param		string		$label				???
	 *      @param		array		$array_option		extrafields array
	 * 		@param		string		$ref_fourn			Supplier price reference
	 *    	@return    	int         	    			>0 if OK, <0 if KO
	 *
	 *    	@see       	add_product
	 */
	function addline($desc, $pu_ht, $qty, $txtva, $txlocaltax1=0, $txlocaltax2=0, $fk_product=0, $remise_percent=0, $price_base_type='HT', $pu_ttc=0, $info_bits=0, $type=0, $rang=-1, $special_code=0, $fk_parent_line=0, $fk_fournprice=0, $pa_ht=0, $label='',$array_option=0, $ref_fourn='')
	{
		global $mysoc;

		dol_syslog(get_class($this)."::addline purchase_requestid=$this->id, desc=$desc, pu_ht=$pu_ht, qty=$qty, txtva=$txtva, fk_product=$fk_product, remise_except=$remise_percent, price_base_type=$price_base_type, pu_ttc=$pu_ttc, info_bits=$info_bits, type=$type");
		include_once DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php';

		// Clean parameters
		if (empty($remise_percent)) $remise_percent=0;
		if (empty($qty)) $qty=0;
		if (empty($info_bits)) $info_bits=0;
		if (empty($rang)) $rang=0;
		if (empty($fk_parent_line) || $fk_parent_line < 0) $fk_parent_line=0;

		$remise_percent=price2num($remise_percent);
		$qty=price2num($qty);
		$pu_ht=price2num($pu_ht);
		$pu_ttc=price2num($pu_ttc);
		$txtva=price2num($txtva);
		$txlocaltax1=price2num($txlocaltax1);
		$txlocaltax2=price2num($txlocaltax2);
		$pa_ht=price2num($pa_ht);
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

		if ($this->statut == 0)
		{
			$this->db->begin();

			// Calcul du total TTC et de la TVA pour la ligne a partir de
			// qty, pu, remise_percent et txtva
			// TRES IMPORTANT: C'est au moment de l'insertion ligne qu'on doit stocker
			// la part ht, tva et ttc, et ce au niveau de la ligne qui a son propre taux tva.

			$localtaxes_type=getLocalTaxesFromRate($txtva,0,$this->thirdparty,$mysoc);
			$txtva = preg_replace('/\s*\(.*\)/','',$txtva);  // Remove code into vatrate.

			$tabprice=calcul_price_total($qty, $pu, $remise_percent, $txtva, $txlocaltax1, $txlocaltax2, 0, $price_base_type, $info_bits, $type, $this->thirdparty, $localtaxes_type, 100, $this->multicurrency_tx);
			$total_ht  = $tabprice[0];
			$total_tva = $tabprice[1];
			$total_ttc = $tabprice[2];
			$total_localtax1 = $tabprice[9];
			$total_localtax2 = $tabprice[10];

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
			$remise = 0;
			if ($remise_percent > 0)
			{
				$remise = round(($pu * $remise_percent / 100), 2);
				$price = $pu - $remise;
			}

			// Insert line
			$this->line=new PurchaserequestLine($this->db);

			$this->line->fk_purchase_request=$this->id;
			$this->line->label=$label;
			$this->line->desc=$desc;
			$this->line->qty=$qty;
			$this->line->tva_tx=$txtva;
			$this->line->localtax1_tx=$txlocaltax1;
			$this->line->localtax2_tx=$txlocaltax2;
			$this->line->localtax1_type = $localtaxes_type[0];
			$this->line->localtax2_type = $localtaxes_type[2];
			$this->line->fk_product=$fk_product;
			$this->line->remise_percent=$remise_percent;
			$this->line->subprice=$pu_ht;
			$this->line->rang=$rangtouse;
			$this->line->info_bits=$info_bits;
			$this->line->total_ht=$total_ht;
			$this->line->total_tva=$total_tva;
			$this->line->total_localtax1=$total_localtax1;
			$this->line->total_localtax2=$total_localtax2;
			$this->line->total_ttc=$total_ttc;
			$this->line->product_type=$type;
			$this->line->special_code=$special_code;
			$this->line->fk_parent_line=$fk_parent_line;

			$this->line->ref_fourn = $this->db->escape($ref_fourn);

			// infos marge
			if (!empty($fk_product) && empty($fk_fournprice) && empty($pa_ht)) {
				// by external module, take lowest buying price
				include_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.product.class.php';
				$productFournisseur = new ProductFournisseur($this->db);
				$productFournisseur->find_min_price_product_fournisseur($fk_product);
				$this->line->fk_fournprice = $productFournisseur->product_fourn_price_id;
			} else {
				$this->line->fk_fournprice = $fk_fournprice;
			}
			$this->line->pa_ht = $pa_ht;

			// Multicurrency
			$this->line->fk_multicurrency			= $this->fk_multicurrency;
			$this->line->multicurrency_code			= $this->multicurrency_code;
			$this->line->multicurrency_subprice		= price2num($pu_ht * $this->multicurrency_tx);
			$this->line->multicurrency_total_ht 	= $multicurrency_total_ht;
			$this->line->multicurrency_total_tva 	= $multicurrency_total_tva;
			$this->line->multicurrency_total_ttc 	= $multicurrency_total_ttc;

			// Mise en option de la ligne
			if (empty($qty) && empty($special_code)) $this->line->special_code=3;

			// TODO deprecated
			$this->line->price=$price;
			$this->line->remise=$remise;

			if (is_array($array_option) && count($array_option)>0) {
				$this->line->array_options=$array_option;
			}

			$result=$this->line->insert();
			if ($result > 0)
			{
				// Reorder if child line
				if (! empty($fk_parent_line)) $this->line_order(true,'DESC');

				// Mise a jour informations denormalisees au niveau de la propale meme
				$result=$this->update_price(1,'auto');	// This method is designed to add line from user input so total calculation must be done using 'auto' mode.
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
	 *  Update a proposal line
	 *
	 *  @param      int			$rowid           	Id de la ligne
	 *  @param      double		$pu		     	  	Prix unitaire (HT ou TTC selon price_base_type)
	 *  @param      double		$qty            	Quantity
	 *  @param      double		$remise_percent  	Remise effectuee sur le produit
	 *  @param      double		$txtva	          	Taux de TVA
	 * 	@param	  	double		$txlocaltax1		Local tax 1 rate
	 *  @param	  	double		$txlocaltax2		Local tax 2 rate
	 *  @param      string		$desc            	Description
	 *	@param	  	double		$price_base_type	HT ou TTC
	 *	@param      int			$info_bits        	Miscellaneous informations
	 *	@param		int			$special_code		Special code (also used by externals modules!)
	 * 	@param		int			$fk_parent_line		Id of parent line (0 in most cases, used by modules adding sublevels into lines).
	 * 	@param		int			$skip_update_total	Keep fields total_xxx to 0 (used for special lines by some modules)
	 *  @param		int			$fk_fournprice		Id of origin supplier price
	 *  @param		int			$pa_ht				Price (without tax) of product when it was bought
	 *  @param		string		$label				???
	 *  @param		int			$type				0/1=Product/service
	 *  @param		array		$array_option		extrafields array
	 * 	@param		string		$ref_fourn			Supplier price reference
	 *  @return     int     		        		0 if OK, <0 if KO
	 */
	function updateline($rowid, $pu, $qty, $remise_percent, $txtva, $txlocaltax1=0, $txlocaltax2=0, $desc='', $price_base_type='HT', $info_bits=0, $special_code=0, $fk_parent_line=0, $skip_update_total=0, $fk_fournprice=0, $pa_ht=0, $label='', $type=0, $array_option=0, $ref_fourn='')
	{
		global $conf,$user,$langs, $mysoc;

		dol_syslog(get_class($this)."::updateLine $rowid, $pu, $qty, $remise_percent, $txtva, $desc, $price_base_type, $info_bits");
		include_once DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php';

		// Clean parameters
		$remise_percent=price2num($remise_percent);
		$qty=price2num($qty);
		$pu = price2num($pu);
		$txtva = price2num($txtva);
		$txlocaltax1=price2num($txlocaltax1);
		$txlocaltax2=price2num($txlocaltax2);
		$pa_ht=price2num($pa_ht);
		if (empty($qty) && empty($special_code)) $special_code=3;    // Set option tag
		if (! empty($qty) && $special_code == 3) $special_code=0;    // Remove option tag

		if ($this->statut == 0)
		{
			$this->db->begin();

			// Calcul du total TTC et de la TVA pour la ligne a partir de
			// qty, pu, remise_percent et txtva
			// TRES IMPORTANT: C'est au moment de l'insertion ligne qu'on doit stocker
			// la part ht, tva et ttc, et ce au niveau de la ligne qui a son propre taux tva.

			$localtaxes_type=getLocalTaxesFromRate($txtva,0,$this->thirdparty,$mysoc);
			$txtva = preg_replace('/\s*\(.*\)/','',$txtva);  // Remove code into vatrate.

			$tabprice=calcul_price_total($qty, $pu, $remise_percent, $txtva, $txlocaltax1, $txlocaltax2, 0, $price_base_type, $info_bits, $type, $this->thirdparty, $localtaxes_type, 100, $this->multicurrency_tx);
			$total_ht  = $tabprice[0];
			$total_tva = $tabprice[1];
			$total_ttc = $tabprice[2];
			$total_localtax1 = $tabprice[9];
			$total_localtax2 = $tabprice[10];

			// MultiCurrency
			$multicurrency_total_ht  = $tabprice[16];
			$multicurrency_total_tva = $tabprice[17];
			$multicurrency_total_ttc = $tabprice[18];

			// Anciens indicateurs: $price, $remise (a ne plus utiliser)
			$price = $pu;
			if ($remise_percent > 0)
			{
				$remise = round(($pu * $remise_percent / 100), 2);
				$price = $pu - $remise;
			}

			// Update line
			$this->line=new PurchaserequestLine($this->db);

			// Stock previous line records
			$staticline=new PurchaserequestLine($this->db);
			$staticline->fetch($rowid);
			$this->line->oldline = $staticline;

			// Reorder if fk_parent_line change
			if (! empty($fk_parent_line) && ! empty($staticline->fk_parent_line) && $fk_parent_line != $staticline->fk_parent_line)
			{
				$rangmax = $this->line_max($fk_parent_line);
				$this->line->rang = $rangmax + 1;
			}

			$this->line->rowid				= $rowid;
			$this->line->label				= $label;
			$this->line->desc				= $desc;
			$this->line->qty				= $qty;
			$this->line->product_type			= $type;
			$this->line->tva_tx				= $txtva;
			$this->line->localtax1_tx		= $txlocaltax1;
			$this->line->localtax2_tx		= $txlocaltax2;
			$this->line->localtax1_type		= $localtaxes_type[0];
			$this->line->localtax2_type		= $localtaxes_type[2];
			$this->line->remise_percent		= $remise_percent;
			$this->line->subprice			= $pu;
			$this->line->info_bits			= $info_bits;
			$this->line->total_ht			= $total_ht;
			$this->line->total_tva			= $total_tva;
			$this->line->total_localtax1	= $total_localtax1;
			$this->line->total_localtax2	= $total_localtax2;
			$this->line->total_ttc			= $total_ttc;
			$this->line->special_code		= $special_code;
			$this->line->fk_parent_line		= $fk_parent_line;
			$this->line->skip_update_total	= $skip_update_total;
			$this->line->ref_fourn	= $ref_fourn;

			// infos marge
			if (!empty($fk_product) && empty($fk_fournprice) && empty($pa_ht)) {
				// by external module, take lowest buying price
				include_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.product.class.php';
				$productFournisseur = new ProductFournisseur($this->db);
				$productFournisseur->find_min_price_product_fournisseur($fk_product);
				$this->line->fk_fournprice = $productFournisseur->product_fourn_price_id;
			} else {
				$this->line->fk_fournprice = $fk_fournprice;
			}
			$this->line->pa_ht = $pa_ht;

			// TODO deprecated
			$this->line->price=$price;
			$this->line->remise=$remise;

			if (is_array($array_option) && count($array_option)>0) {
				$this->line->array_options=$array_option;
			}

			// Multicurrency
			$this->line->multicurrency_subprice		= price2num($pu * $this->multicurrency_tx);
			$this->line->multicurrency_total_ht 	= $multicurrency_total_ht;
			$this->line->multicurrency_total_tva 	= $multicurrency_total_tva;
			$this->line->multicurrency_total_ttc 	= $multicurrency_total_ttc;

			$result=$this->line->update();
			if ($result > 0)
			{
				// Reorder if child line
				if (! empty($fk_parent_line)) $this->line_order(true,'DESC');

				$this->update_price(1);

				$this->fk_purchase_request = $this->id;
				$this->rowid = $rowid;

				$this->db->commit();
				return $result;
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
			dol_syslog(get_class($this)."::updateline Erreur -2 SupplierProposal en mode incompatible pour cette action");
			return -2;
		}
	}


	/**
	 *  Delete detail line
	 *
	 *  @param		int		$lineid			Id of line to delete
	 *  @return     int         			>0 if OK, <0 if KO
	 */
	function deleteline($lineid)
	{
		if ($this->statut == 0)
		{
			$line=new PurchaserequestLine($this->db);

			// For triggers
			$line->fetch($lineid);

			if ($line->delete() > 0)
			{
				$this->update_price(1);

				return 1;
			}
			else
			{
				return -1;
			}
		}
		else
		{
			return -2;
		}
	}
	/**
	 *	Insert into DB a purchase_request object completely defined by its data members (ex, results from copy).
	 *
	 *	@param 		User	$user	User that create
	 *	@return    	int				Id of the new object if ok, <0 if ko
	 *	@see       	create
	 */
	function create_from($user)
	{
		$this->products=$this->lines;

		return $this->create($user);
	}

	/**
	 *		Load an object from its id and create a new one in database
	 *
	 *		@param		int				$socid			Id of thirdparty
	 * 	 	@return		int								New id of clone
	 */
	function createFromClone($socid=0)
	{
		global $user,$langs,$conf,$hookmanager;

		$error=0;
		$now=dol_now();

		$this->db->begin();

		// get extrafields so they will be clone
		foreach($this->lines as $line)
			$line->fetch_optionals($line->rowid);

		// Load source object
		$objFrom = clone $this;

		$objsoc=new Societe($this->db);

		// Change socid if needed
		if (! empty($socid) && $socid != $this->socid)
		{
			if ($objsoc->fetch($socid) > 0)
			{
				$this->socid 				= $objsoc->id;
				$this->cond_reglement_id	= (! empty($objsoc->cond_reglement_id) ? $objsoc->cond_reglement_id : 0);
				$this->mode_reglement_id	= (! empty($objsoc->mode_reglement_id) ? $objsoc->mode_reglement_id : 0);
				$this->fk_project			= '';
			}

			// TODO Change product price if multi-prices
		}
		else
		{
			$objsoc->fetch($this->socid);
		}

		$this->id=0;
		$this->statut=0;

		if (empty($conf->global->PURCHASE_SUPPLIER_ADDON_NUMBER) || ! is_readable(DOL_DOCUMENT_ROOT ."/purchase/core/modules/purchase_request/".$conf->global->PURCHASE_SUPPLIER_ADDON_NUMBER.".php"))
		{
			$this->error='ErrorSetupNotComplete';
			return -1;
		}

		// Clear fields
		$this->user_author	= $user->id;
		$this->user_valid	= '';
		$this->date			= $now;

		// Set ref
		require_once DOL_DOCUMENT_ROOT ."/purchase/core/modules/purchase_request/".$conf->global->PURCHASE_SUPPLIER_ADDON_NUMBER.'.php';
		$obj = $conf->global->PURCHASE_SUPPLIER_ADDON_NUMBER;
		$modSupplierProposal = new $obj;
		$this->ref = $modSupplierProposal->getNextValue($objsoc,$this);

		// Create clone
		$result=$this->create($user);
		if ($result < 0) $error++;

		if (! $error)
		{
			// Hook of thirdparty module
			if (is_object($hookmanager))
			{
				$parameters=array('objFrom'=>$objFrom);
				$action='';
				$reshook=$hookmanager->executeHooks('createFrom',$parameters,$this,$action);    // Note that $action and $object may have been modified by some hooks
				if ($reshook < 0) $error++;
			}

			// Call trigger
			$result=$this->call_trigger('PURCHASE_REQUEST_CLONE',$user);
			if ($result < 0) { $error++; }
			// End call triggers
		}

		// End
		if (! $error)
		{
			$this->db->commit();
			return $this->id;
		}
		else
		{
			$this->db->rollback();
			return -1;
		}
	}
	/**
	 *	Update value of extrafields on the proposal
	 *
	 *	@param      User	$user       Object user that modify
	 *	@return     int         		<0 if ko, >0 if ok
	 */
	function update_extrafields($user)
	{
		$action='update';

		// Actions on extra fields (by external module or standard code)
		$hookmanager->initHooks(array('purchase_requestdao'));
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

		if (!$error)
		{
			return 1;
		}
		else
		{
			return -1;
		}

	}

	/**
	 *  Set status to validated
	 *
	 *  @param	User	$user       Object user that validate
	 *  @param	int		$notrigger	1=Does not execute triggers, 0= execuete triggers
	 *  @return int         		<0 if KO, >=0 if OK
	 */
	function valid($user, $notrigger=0)
	{
		require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

		global $conf,$langs;

		$error=0;
		$now=dol_now();

		if ((empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->purchase->request->creer))
			|| (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && ! empty($user->rights->supplier->request->validate_advance)))
		{
			$this->db->begin();

			// Numbering module definition
			$soc = new Societe($this->db);
			$soc->fetch($this->socid);

			// Define new ref
			if (! $error && (preg_match('/^[\(]?PROV/i', $this->ref) || empty($this->ref))) // empty should not happened, but when it occurs, the test save life
			{
				$num = $this->getNextNumRef($soc);
			}
			else
			{
				$num = $this->ref;
			}
			$this->newref = $num;

			$sql = "UPDATE ".MAIN_DB_PREFIX."purchase_request";
			$sql.= " SET ref = '".$num."',";
			$sql.= " fk_statut = 1, date_valid='".$this->db->idate($now)."', fk_user_valid=".$user->id;
			$sql.= " WHERE rowid = ".$this->id." AND fk_statut = 0";

			dol_syslog(get_class($this)."::valid", LOG_DEBUG);
			$resql=$this->db->query($sql);
			if (! $resql)
			{
				dol_print_error($this->db);
				$error++;
			}

			// Trigger calls
			if (! $error && ! $notrigger)
			{
				// Call trigger
				$result=$this->call_trigger('PURCHASE_REQUEST_VALIDATE',$user);
				if ($result < 0) { $error++; }
				// End call triggers
			}

			if (! $error)
			{
				$this->oldref = $this->ref;

				// Rename directory if dir was a temporary ref
				if (preg_match('/^[\(]?PROV/i', $this->ref))
				{
					// Rename of propal directory ($this->ref = old ref, $num = new ref)
					// to  not lose the linked files
					$oldref = dol_sanitizeFileName($this->ref);
					$newref = dol_sanitizeFileName($num);
					$dirsource = $conf->purchase_request->dir_output.'/'.$oldref;
					$dirdest = $conf->purchase_request->dir_output.'/'.$newref;

					if (file_exists($dirsource))
					{
						dol_syslog(get_class($this)."::validate rename dir ".$dirsource." into ".$dirdest);
						if (@rename($dirsource, $dirdest))
						{
							dol_syslog("Rename ok");
							// Rename docs starting with $oldref with $newref
							$listoffiles=dol_dir_list($conf->purchase_request->dir_output.'/'.$newref, 'files', 1, '^'.preg_quote($oldref,'/'));
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

				$this->ref=$num;
				$this->brouillon=0;
				$this->statut = 1;
				$this->user_valid_id=$user->id;
				$this->datev=$now;

				$this->db->commit();
				return 1;
			}
			else
			{
				$this->db->rollback();
				return -1;
			}
		}
	}

	/**
	 *	Set delivery date
	 *
	 *	@param      User 		$user        		Object user that modify
	 *	@param      int			$date_livraison     Delivery date
	 *	@return     int         					<0 if ko, >0 if ok
	 */
	function set_date_livraison($user, $date_livraison)
	{
		if (! empty($user->rights->purchase_request->creer))
		{
			$sql = "UPDATE ".MAIN_DB_PREFIX."purchase_request ";
			$sql.= " SET date_livraison = ".($date_livraison!=''?"'".$this->db->idate($date_livraison)."'":'null');
			$sql.= " WHERE rowid = ".$this->id;

			if ($this->db->query($sql))
			{
				$this->date_livraison = $date_livraison;
				return 1;
			}
			else
			{
				$this->error=$this->db->error();
				dol_syslog(get_class($this)."::set_date_livraison Erreur SQL");
				return -1;
			}
		}
	}

	/**
	 *	Set an overall discount on the proposal
	 *
	 *	@param      User	$user       Object user that modify
	 *	@param      double	$remise      Amount discount
	 *	@return     int         		<0 if ko, >0 if ok
	 */
	function set_remise_percent($user, $remise)
	{
		$remise=trim($remise)?trim($remise):0;

		if (! empty($user->rights->purchase_request->creer))
		{
			$remise = price2num($remise);

			$sql = "UPDATE ".MAIN_DB_PREFIX."purchase_request SET remise_percent = ".$remise;
			$sql.= " WHERE rowid = ".$this->id." AND fk_statut = 0";

			if ($this->db->query($sql) )
			{
				$this->remise_percent = $remise;
				$this->update_price(1);
				return 1;
			}
			else
			{
				$this->error=$this->db->error();
				return -1;
			}
		}
	}


	/**
	 *	Set an absolute overall discount on the proposal
	 *
	 *	@param      User	$user        Object user that modify
	 *	@param      double	$remise      Amount discount
	 *	@return     int         		<0 if ko, >0 if ok
	 */
	function set_remise_absolue($user, $remise)
	{
		$remise=trim($remise)?trim($remise):0;

		if (! empty($user->rights->purchase_request->creer))
		{
			$remise = price2num($remise);

			$sql = "UPDATE ".MAIN_DB_PREFIX."purchase_request ";
			$sql.= " SET remise_absolue = ".$remise;
			$sql.= " WHERE rowid = ".$this->id." AND fk_statut = 0";

			if ($this->db->query($sql) )
			{
				$this->remise_absolue = $remise;
				$this->update_price(1);
				return 1;
			}
			else
			{
				$this->error=$this->db->error();
				return -1;
			}
		}
	}



	/**
	 *	Reopen the commercial proposal
	 *
	 *	@param      User	$user		Object user that close
	 *	@param      int		$statut		Statut
	 *	@param      string	$note		Comment
	 *  @param		int		$notrigger	1=Does not execute triggers, 0= execuete triggers
	 *	@return     int         		<0 if KO, >0 if OK
	 */
	function reopen($user, $statut, $note='', $notrigger=0)
	{
		global $langs,$conf;

		$this->statut = $statut;
		$error=0;

		$sql = "UPDATE ".MAIN_DB_PREFIX."purchase_request";
		$sql.= " SET fk_statut = ".$this->statut.",";
		if (! empty($note)) $sql.= " note_private = '".$this->db->escape($note)."',";
		$sql.= " date_cloture=NULL, fk_user_cloture=NULL";
		$sql.= " WHERE rowid = ".$this->id;

		$this->db->begin();

		dol_syslog(get_class($this)."::reopen", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$error++; $this->errors[]="Error ".$this->db->lasterror();
		}
		if (! $error)
		{
			if (! $notrigger)
			{
				// Call trigger
				$result=$this->call_trigger('PURCHASE_REQUEST_REOPEN',$user);
				if ($result < 0) { $error++; }
				// End call triggers
			}
		}

		// Commit or rollback
		if ($error)
		{
			if (!empty($this->errors))
			{
				foreach($this->errors as $errmsg)
				{
					dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
					$this->error.=($this->error?', '.$errmsg:$errmsg);
				}
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


	/**
	 *	Close the askprice
	 *
	 *	@param      User	$user		Object user that close
	 *	@param      int		$statut		Statut
	 *	@param      string	$note		Comment
	 *	@return     int         		<0 if KO, >0 if OK
	 */
	function cloture($user, $statut, $note)
	{
		global $langs,$conf;

		$this->statut = $statut;
		$error=0;
		$now=dol_now();

		$this->db->begin();

		$sql = "UPDATE ".MAIN_DB_PREFIX."purchase_request";
		$sql.= " SET fk_statut = ".$statut.", note_private = '".$this->db->escape($note)."', date_cloture='".$this->db->idate($now)."', fk_user_cloture=".$user->id;
		$sql.= " WHERE rowid = ".$this->id;

		$resql=$this->db->query($sql);
		if ($resql)
		{
			$modelpdf=$conf->global->PURCHASE_SUPPLIER_ADDON_NUMBER_PDF_ODT_CLOSED?$conf->global->PURCHASE_SUPPLIER_ADDON_NUMBER_PDF_ODT_CLOSED:$this->modelpdf;
			$trigger_name='PURCHASE_REQUEST_CLOSE_REFUSED';

			if ($statut == 2)
			{
				$trigger_name='PURCHASE_REQUEST_CLOSE_SIGNED';
				$modelpdf=$conf->global->PURCHASE_SUPPLIER_ADDON_NUMBER_PDF_ODT_TOBILL?$conf->global->PURCHASE_SUPPLIER_ADDON_NUMBER_PDF_ODT_TOBILL:$this->modelpdf;

				if (! empty($conf->global->PURCHASE_REQUEST_UPDATE_PRICE_ON_SUPPlIER_PROPOSAL))     // TODO This option was not tested correctly. Error if product ref does not exists
				{
					$result = $this->updateOrCreatePriceFournisseur($user);
				}

			}
			if ($statut == 4)
			{
				$trigger_name='PURCHASE_REQUEST_CLASSIFY_BILLED';
			}

			if (empty($conf->global->MAIN_DISABLE_PDF_AUTOUPDATE))
			{
				// Define output language
				$outputlangs = $langs;
				if (! empty($conf->global->MAIN_MULTILANGS))
				{
					$outputlangs = new Translate("",$conf);
					$newlang=(GETPOST('lang_id') ? GETPOST('lang_id') : $this->thirdparty->default_lang);
					$outputlangs->setDefaultLang($newlang);
				}
				//$ret=$object->fetch($id);    // Reload to get new records
				$this->generateDocument($modelpdf, $outputlangs, $hidedetails, $hidedesc, $hideref);
			}

			// Call trigger
			$result=$this->call_trigger($trigger_name,$user);
			if ($result < 0) { $error++; }
			// End call triggers

			if ( ! $error )
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
		else
		{
			$this->error=$this->db->lasterror();
			$this->errors[]=$this->db->lasterror();
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 *	Add or update supplier price according to result of proposal
	 *
	 *	@param     User	    $user       Object user
	 *  @return    int                  > 0 if OK
	 */
	function updateOrCreatePriceFournisseur($user)
	{
		$productsupplier = new ProductFournisseur($this->db);

		dol_syslog(get_class($this)."::updateOrCreatePriceFournisseur", LOG_DEBUG);
		foreach ($this->lines as $product)
		{
			if ($product->subprice <= 0) continue;

			$idProductFourn = $productsupplier->find_min_price_product_fournisseur($product->fk_product, $product->qty);
			$res = $productsupplier->fetch($idProductFourn);

			if ($productsupplier->id) {
				if ($productsupplier->fourn_qty == $product->qty) {
					$this->updatePriceFournisseur($productsupplier->product_fourn_price_id, $product, $user);
				} else {
					$this->createPriceFournisseur($product, $user);
				}
			} else {
				$this->createPriceFournisseur($product, $user);
			}
		}

		return 1;
	}

	/**
	 *	Upate ProductFournisseur
	 *
	 * 	@param		int 	$idProductFournPrice	id of llx_product_fournisseur_price
	 * 	@param		int 	$product				contain informations to update
	 *	@param      User	$user					Object user
	 *	@return     int         					<0 if KO, >0 if OK
	 */
	function updatePriceFournisseur($idProductFournPrice, $product, $user) {
		$price=price2num($product->subprice*$product->qty,'MU');
		$unitPrice = price2num($product->subprice,'MU');

		$sql = 'UPDATE '.MAIN_DB_PREFIX.'product_fournisseur_price SET '.(!empty($product->ref_fourn) ? 'ref_fourn = "'.$product->ref_fourn.'", ' : '').' price ='.$price.', unitprice ='.$unitPrice.' WHERE rowid = '.$idProductFournPrice;

		$resql = $this->db->query($sql);
		if (!$resql) {
			$this->error=$this->db->error();
			$this->db->rollback();
			return -1;
		}
	}



	 /**
	 *	Create ProductFournisseur
	 *
	 *	@param		Product 	$product	Object Product
	 *	@param      User		$user		Object user
	 *	@return     int         			<0 if KO, >0 if OK
	 */
	 function createPriceFournisseur($product, $user) {
	 	$price=price2num($product->subprice*$product->qty,'MU');
	 	$qty=price2num($product->qty);
	 	$unitPrice = price2num($product->subprice,'MU');
	 	$now=dol_now();

	 	$values = array(
	 		"'".$this->db->idate($now)."'",
	 		$product->fk_product,
	 		$this->thirdparty->id,
	 		"'".$product->ref_fourn."'",
	 		$price,
	 		$qty,
	 		$unitPrice,
	 		$product->tva_tx,
	 		$user->id
	 		);

	 	$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'product_fournisseur_price ';
	 	$sql .= '(datec, fk_product, fk_soc, ref_fourn, price, quantity, unitprice, tva_tx, fk_user) VALUES ('.implode(',', $values).')';

	 	$resql = $this->db->query($sql);
	 	if (!$resql) {
	 		$this->error=$this->db->error();
	 		$this->db->rollback();
	 		return -1;
	 	}
	 }

	/**
	 *	Set draft status
	 *
	 *	@param		User	$user		Object user that modify
	 *	@return		int					<0 if KO, >0 if OK
	 */
	function set_draft($user)
	{
		global $conf,$langs;

		$sql = "UPDATE ".MAIN_DB_PREFIX."purchase_request SET fk_statut = 0";
		$sql.= " WHERE rowid = ".$this->id;

		if ($this->db->query($sql))
		{
			$this->statut = 0;
			$this->brouillon = 1;
			return 1;
		}
		else
		{
			return -1;
		}
	}


	/**
	 *    Return list of askprice (eventually filtered on user) into an array
	 *
	 *    @param	int		$shortlist			0=Return array[id]=ref, 1=Return array[](id=>id,ref=>ref,name=>name)
	 *    @param	int		$draft				0=not draft, 1=draft
	 *    @param	int		$notcurrentuser		0=all user, 1=not current user
	 *    @param    int		$socid				Id third pary
	 *    @param    int		$limit				For pagination
	 *    @param    int		$offset				For pagination
	 *    @param    string	$sortfield			Sort criteria
	 *    @param    string	$sortorder			Sort order
	 *    @return	int		       				-1 if KO, array with result if OK
	 */
	function liste_array($shortlist=0, $draft=0, $notcurrentuser=0, $socid=0, $limit=0, $offset=0, $sortfield='p.datec', $sortorder='DESC')
	{
		global $conf,$user;

		$ga = array();

		$sql = "SELECT s.rowid, s.nom as name, s.client,";
		$sql.= " p.rowid as purchase_requestid, p.fk_statut, p.total_ht, p.ref, p.remise, ";
		$sql.= " p.datep as dp, p.fin_validite as datelimite";
		if (! $user->rights->societe->client->voir && ! $socid) $sql .= ", sc.fk_soc, sc.fk_user";
		$sql.= " FROM ".MAIN_DB_PREFIX."societe as s, ".MAIN_DB_PREFIX."purchase_request as p, ".MAIN_DB_PREFIX."c_propalst as c";
		if (! $user->rights->societe->client->voir && ! $socid) $sql .= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
		$sql.= " WHERE p.entity = ".$conf->entity;
		$sql.= " AND p.fk_soc = s.rowid";
		$sql.= " AND p.fk_statut = c.id";
		if (! $user->rights->societe->client->voir && ! $socid) //restriction
		{
			$sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$user->id;
		}
		if ($socid) $sql.= " AND s.rowid = ".$socid;
		if ($draft)	$sql.= " AND p.fk_statut = 0";
		if ($notcurrentuser > 0) $sql.= " AND p.fk_user_author <> ".$user->id;
		$sql.= $this->db->order($sortfield,$sortorder);
		$sql.= $this->db->plimit($limit,$offset);

		$result=$this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($result);

					if ($shortlist == 1)
					{
						$ga[$obj->purchase_requestid] = $obj->ref;
					}
					else if ($shortlist == 2)
					{
						$ga[$obj->purchase_requestid] = $obj->ref.' ('.$obj->name.')';
					}
					else
					{
						$ga[$i]['id']	= $obj->purchase_requestid;
						$ga[$i]['ref'] 	= $obj->ref;
						$ga[$i]['name'] = $obj->name;
					}

					$i++;
				}
			}
			return $ga;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}
	/**
	 *	Object SupplierProposal Information
	 *
	 * 	@param	int		$id		Proposal id
	 *  @return	void
	 */
	function info($id)
	{
		$sql = "SELECT c.rowid, ";
		$sql.= " c.datec, c.date_valid as datev, c.date_cloture as dateo,";
		$sql.= " c.fk_user_author, c.fk_user_valid, c.fk_user_cloture";
		$sql.= " FROM ".MAIN_DB_PREFIX."purchase_request as c";
		$sql.= " WHERE c.rowid = ".$id;

		$result = $this->db->query($sql);

		if ($result)
		{
			if ($this->db->num_rows($result))
			{
				$obj = $this->db->fetch_object($result);

				$this->id                = $obj->rowid;

				$this->date_creation     = $this->db->jdate($obj->datec);
				$this->date_validation   = $this->db->jdate($obj->datev);
				$this->date_cloture      = $this->db->jdate($obj->dateo);

				$cuser = new User($this->db);
				$cuser->fetch($obj->fk_user_author);
				$this->user_creation     = $cuser;

				if ($obj->fk_user_valid)
				{
					$vuser = new User($this->db);
					$vuser->fetch($obj->fk_user_valid);
					$this->user_validation     = $vuser;
				}

				if ($obj->fk_user_cloture)
				{
					$cluser = new User($this->db);
					$cluser->fetch($obj->fk_user_cloture);
					$this->user_cloture     = $cluser;
				}


			}
			$this->db->free($result);

		}
		else
		{
			dol_print_error($this->db);
		}
	}
	/**
	 *      Load indicators for dashboard (this->nbtodo and this->nbtodolate)
	 *
	 *      @param          User	$user   Object user
	 *      @param          int		$mode   "opened" for askprice to close, "signed" for proposal to invoice
	 *      @return         int     		<0 if KO, >0 if OK
	 */
	function load_board($user,$mode)
	{
		global $conf, $user, $langs;

		$now=dol_now();

		$this->nbtodo=$this->nbtodolate=0;
		$clause = " WHERE";

		$sql = "SELECT p.rowid, p.ref, p.datec as datec";
		$sql.= " FROM ".MAIN_DB_PREFIX."purchase_request as p";
		if (!$user->rights->societe->client->voir && !$user->societe_id)
		{
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe_commerciaux as sc ON p.fk_soc = sc.fk_soc";
			$sql.= " WHERE sc.fk_user = " .$user->id;
			$clause = " AND";
		}
		$sql.= $clause." p.entity = ".$conf->entity;
		if ($mode == 'opened') $sql.= " AND p.fk_statut = 1";
		if ($mode == 'signed') $sql.= " AND p.fk_statut = 2";
		if ($user->societe_id) $sql.= " AND p.fk_soc = ".$user->societe_id;

		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($mode == 'opened') {
				$delay_warning=$conf->purchase_request->cloture->warning_delay;
				$statut = self::STATUS_VALIDATED;
				$label = $langs->trans("SupplierProposalsToClose");
			}
			if ($mode == 'signed') {
				$delay_warning=$conf->purchase_request->facturation->warning_delay;
				$statut = self::STATUS_SIGNED;
				$label = $langs->trans("SupplierProposalsToProcess");      // May be billed or ordered
			}

			$response = new WorkboardResponse();
			$response->warning_delay = $delay_warning/60/60/24;
			$response->label = $label;
			$response->url = DOL_URL_ROOT.'/purchase/request/list.php?viewstatut='.$statut;
			$response->img = img_object($langs->trans("SupplierProposals"),"propal");

			// This assignment in condition is not a bug. It allows walking the results.
			while ($obj=$this->db->fetch_object($resql))
			{
				$response->nbtodo++;
				if ($mode == 'opened')
				{
					$datelimit = $this->db->jdate($obj->datefin);
					if ($datelimit < ($now - $delay_warning))
					{
						$response->nbtodolate++;
					}
				}
				// TODO Definir regle des propales a facturer en retard
				// if ($mode == 'signed' && ! count($this->FactureListeArray($obj->rowid))) $this->nbtodolate++;
			}
			return $response;
		}
		else
		{
			$this->error=$this->db->lasterror();
			return -1;
		}
	}

	/**
	 *      Charge indicateurs this->nb de tableau de bord
	 *
	 *      @return     int         <0 if ko, >0 if ok
	 */
	function load_state_board()
	{
		global $conf, $user;

		$this->nb=array();
		$clause = "WHERE";

		$sql = "SELECT count(p.rowid) as nb";
		$sql.= " FROM ".MAIN_DB_PREFIX."purchase_request as p";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON p.fk_soc = s.rowid";
		if (!$user->rights->societe->client->voir && !$user->societe_id)
		{
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe_commerciaux as sc ON s.rowid = sc.fk_soc";
			$sql.= " WHERE sc.fk_user = " .$user->id;
			$clause = "AND";
		}
		$sql.= " ".$clause." p.entity = ".$conf->entity;

		$resql=$this->db->query($sql);
		if ($resql)
		{
			// This assignment in condition is not a bug. It allows walking the results.
			while ($obj=$this->db->fetch_object($resql))
			{
				$this->nb["askprice"]=$obj->nb;
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			dol_print_error($this->db);
			$this->error=$this->db->lasterror();
			return -1;
		}
	}


	/**
	 *  Returns the reference to the following non used Proposal used depending on the active numbering module
	 *  defined into PURCHASE_SUPPLIER_ADDON_NUMBER
	 *
	 *  @param	Societe		$soc  	Object thirdparty
	 *  @return string      		Reference libre pour la propale
	 */
	function getNextNumRef($soc)
	{
		global $conf, $db, $langs;
		$langs->load("purchase");

		if (! empty($conf->global->PURCHASE_SUPPLIER_ADDON_NUMBER))
		{
			$mybool=false;

			$file = $conf->global->PURCHASE_SUPPLIER_ADDON_NUMBER.".php";
			$classname = $conf->global->PURCHASE_SUPPLIER_ADDON_NUMBER;

			// Include file with class
			$dirmodels = array_merge(array('/'), (array) $conf->modules_parts['models']);
			foreach ($dirmodels as $reldir) {

				$dir = dol_buildpath($reldir."purchase/core/modules/");

				// Load file with numbering class (if found)
				$mybool|=@include_once $dir.$file;
			}

			if (! $mybool)
			{
				dol_print_error('',"Failed to include file ".$file);
				return '';
			}

			$obj = new $classname();
			$numref = "";
			$numref = $obj->getNextValue($soc,$this);

			if ($numref != "")
			{
				return $numref;
			}
			else
			{
				$this->error=$obj->error;
				return "";
			}
		}
		else
		{
			$langs->load("errors");
			print $langs->trans("Error")." ".$langs->trans("ErrorModuleSetupNotComplete");
			return "";
		}
	}
	/**
	 * 	Retrieve an array of supplier proposal lines
	 *
	 * 	@return int		>0 if OK, <0 if KO
	 */
	function getLinesArray()
	{
		// For other object, here we call fetch_lines. But fetch_lines does not exists on supplier proposal

		$sql = 'SELECT pt.rowid, pt.label as custom_label, pt.description, pt.fk_product, pt.fk_remise_except,';
		$sql.= ' pt.ref AS refline, pt.fk_purchase_request,';
		$sql.= ' pt.qty, pt.tva_tx, pt.remise_percent, pt.subprice, pt.price, pt.info_bits,';
		$sql.= ' pt.total_ht, pt.total_tva, pt.total_ttc, pt.fk_product_fournisseur_price as fk_fournprice, pt.buy_price_ht as pa_ht, pt.special_code, pt.localtax1_tx, pt.localtax2_tx,pt.fk_unit,';
		$sql.= " pt.fk_fabrication, pt.fk_fabricationdet, pt.fk_projet, pt.fk_projet_task, pt.fk_jobs, pt.fk_jobsdet, pt.fk_structure,";
		$sql.= ' pt.product_type, pt.rang, pt.fk_parent_line,';
		$sql.= ' p.label as product_label, p.ref, p.fk_product_type, p.rowid as prodid,';
		$sql.= ' p.description as product_desc, pt.ref_fourn as ref_produit_fourn';
		$sql.= ' ,pt.fk_multicurrency, pt.multicurrency_code, pt.multicurrency_subprice, pt.multicurrency_total_ht, pt.multicurrency_total_tva, pt.multicurrency_total_ttc';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'purchase_requestdet as pt';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON pt.fk_product=p.rowid';
		$sql.= ' WHERE pt.fk_purchase_request = '.$this->id;
		$sql.= ' ORDER BY pt.rang ASC, pt.rowid';

		dol_syslog(get_class($this).'::getLinesArray', LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			$i = 0;

			while ($i < $num)
			{
				$obj = $this->db->fetch_object($resql);

				$this->lines[$i]					= new PurchaserequestLine($this->db);
				$this->lines[$i]->id				= $obj->rowid; // for backward compatibility
				$this->lines[$i]->rowid				= $obj->rowid;
				$this->lines[$i]->fk_purchase_request		= $obj->fk_purchase_request;
				$this->lines[$i]->refline			= $obj->refline;
				$this->lines[$i]->label 			= $obj->custom_label;
				$this->lines[$i]->description 		= $obj->description;
				$this->lines[$i]->fk_product		= $obj->fk_product;
				$this->lines[$i]->ref				= $obj->ref;
				$this->lines[$i]->product_label		= $obj->product_label;
				$this->lines[$i]->product_desc		= $obj->product_desc;
				$this->lines[$i]->fk_product_type	= $obj->fk_product_type;  // deprecated
				$this->lines[$i]->product_type		= $obj->product_type;
				$this->lines[$i]->qty				= $obj->qty;
				$this->lines[$i]->subprice			= $obj->subprice;
				$this->lines[$i]->price			= $obj->price;
				$this->lines[$i]->fk_remise_except 	= $obj->fk_remise_except;
				$this->lines[$i]->fk_fabrication 	= $obj->fk_fabrication;
				$this->lines[$i]->fk_fabricationdet 	= $obj->fk_fabricationdet;
				$this->lines[$i]->fk_projet 		= $obj->fk_projet;
				$this->lines[$i]->fk_projet_task		= $obj->fk_projet_task;
				$this->lines[$i]->fk_jobs 		= $obj->fk_jobs;
				$this->lines[$i]->fk_jobsdet 		= $obj->fk_jobsdet;
				$this->lines[$i]->fk_structure 		= $obj->fk_structure;
				$this->lines[$i]->remise_percent	= $obj->remise_percent;
				$this->lines[$i]->tva_tx			= $obj->tva_tx;
				$this->lines[$i]->info_bits			= $obj->info_bits;
				$this->lines[$i]->total_ht			= $obj->total_ht;
				$this->lines[$i]->total_tva			= $obj->total_tva;
				$this->lines[$i]->total_ttc			= $obj->total_ttc;
				$this->lines[$i]->fk_fournprice		= $obj->fk_fournprice;
				$marginInfos						= getMarginInfos($obj->subprice, $obj->remise_percent, $obj->tva_tx, $obj->localtax1_tx, $obj->localtax2_tx, $this->lines[$i]->fk_fournprice, $obj->pa_ht);
				$this->lines[$i]->pa_ht				= $marginInfos[0];
				$this->lines[$i]->marge_tx			= $marginInfos[1];
				$this->lines[$i]->marque_tx			= $marginInfos[2];
				$this->lines[$i]->fk_parent_line	= $obj->fk_parent_line;
				$this->lines[$i]->special_code		= $obj->special_code;
				$this->lines[$i]->rang				= $obj->rang;

				$this->lines[$i]->ref_fourn				= $obj->ref_produit_fourn;

				// Multicurrency
				$this->lines[$i]->fk_multicurrency 			= $obj->fk_multicurrency;
				$this->lines[$i]->multicurrency_code 		= $obj->multicurrency_code;
				$this->lines[$i]->multicurrency_subprice 	= $obj->multicurrency_subprice;
				$this->lines[$i]->multicurrency_total_ht 	= $obj->multicurrency_total_ht;
				$this->lines[$i]->multicurrency_total_tva 	= $obj->multicurrency_total_tva;
				$this->lines[$i]->multicurrency_total_ttc 	= $obj->multicurrency_total_ttc;

				$i++;
			}
			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error=$this->db->error();
			return -1;
		}
	}

	/**
	 * 	Retrieve an array of supplier proposal lines
	 *
	 * 	@return int		>0 if OK, <0 if KO
	 */
	function fetch_lines()
	{
		// For other object, here we call fetch_lines. But fetch_lines does not exists on supplier proposal

		$sql = 'SELECT pt.rowid, pt.label as custom_label, pt.description, pt.fk_product, ';
		$sql.= " pt.ref AS refline, pt.fk_purchase_request,";
		$sql.= ' pt.qty, pt.tva_tx, pt.subprice, pt.price, pt.info_bits,';
		$sql.= ' pt.special_code, pt.fk_poa_partida_pre_det, pt.fk_commande_fournisseurdet, ';
		$sql.= ' pt.product_type, pt.rang, pt.fk_parent_line, pt.fk_unit, pt.total_ttc, pt.total_ht, ';
		$sql.= " pt.fk_fabrication, pt.fk_fabricationdet, pt.fk_projet, pt.fk_projet_task, pt.fk_jobs, pt.fk_jobsdet, pt.fk_structure, pt.fk_poa, pt.partida,";
		$sql.= ' p.label as product_label, p.ref, p.fk_product_type, p.rowid as prodid,';
		$sql.= ' p.description as product_desc, pt.ref_fourn as ref_produit_fourn';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'purchase_requestdet as pt';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON pt.fk_product=p.rowid';
		$sql.= ' WHERE pt.fk_purchase_request = '.$this->id;
		$sql.= ' ORDER BY pt.rang ASC, pt.rowid';

		dol_syslog(get_class($this).'::getLinesArray', LOG_DEBUG);
		$resql = $this->db->query($sql);

		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			$i = 0;

			while ($i < $num)
			{
				$obj = $this->db->fetch_object($resql);

				$this->lines[$i]					= new PurchaserequestLigneext($this->db);
				$this->lines[$i]->id				= $obj->rowid; // for backward compatibility
				$this->lines[$i]->rowid				= $obj->rowid;
				$this->lines[$i]->fk_purchase_request		= $obj->fk_purchase_request;
				$this->lines[$i]->refline			= $obj->refline;
				$this->lines[$i]->label 			= $obj->custom_label;
				$this->lines[$i]->description 		= $obj->description;
				$this->lines[$i]->desc 			= $obj->description;
				$this->lines[$i]->fk_product		= $obj->fk_product;
				$this->lines[$i]->ref			= $obj->ref;
				$this->lines[$i]->product_label		= $obj->product_label;
				$this->lines[$i]->product_desc		= $obj->product_desc;
				$this->lines[$i]->fk_product_type	= $obj->fk_product_type;  // deprecated
				$this->lines[$i]->product_type		= $obj->product_type;
				$this->lines[$i]->qty			= $obj->qty;
				$this->lines[$i]->fk_unit		= $obj->fk_unit;
				$this->lines[$i]->unit			= $this->lines[$i]->getLabelOfUnit();
				$this->lines[$i]->subprice		= $obj->subprice;
				$this->lines[$i]->price			= $obj->price;
				$this->lines[$i]->fk_remise_except 	= $obj->fk_remise_except;
				$this->lines[$i]->fk_fabrication 	= $obj->fk_fabrication;
				$this->lines[$i]->fk_fabricationdet 	= $obj->fk_fabricationdet;
				$this->lines[$i]->fk_projet 		= $obj->fk_projet;
				$this->lines[$i]->fk_projet_task		= $obj->fk_projet_task;
				$this->lines[$i]->fk_jobs 		= $obj->fk_jobs;
				$this->lines[$i]->fk_jobsdet 		= $obj->fk_jobsdet;
				$this->lines[$i]->fk_structure 		= $obj->fk_structure;
				$this->lines[$i]->fk_poa 		= $obj->fk_poa;
				$this->lines[$i]->fk_poa_partida_pre_det = $obj->fk_poa_partida_pre_det;
				$this->lines[$i]->fk_commande_fournisseurdet = $obj->fk_commande_fournisseurdet;
				$this->lines[$i]->partida 		= $obj->partida;
				$this->lines[$i]->remise_percent	= $obj->remise_percent;
				$this->lines[$i]->tva_tx		= $obj->tva_tx;
				$this->lines[$i]->info_bits		= $obj->info_bits;
				$this->lines[$i]->total_ht		= $obj->total_ht;
				$this->lines[$i]->total_tva		= $obj->total_tva;
				$this->lines[$i]->total_ttc		= $obj->total_ttc;
				$this->lines[$i]->fk_fournprice		= $obj->fk_fournprice;
				//$marginInfos						= getMarginInfos($obj->subprice, $obj->remise_percent, $obj->tva_tx, $obj->localtax1_tx, $obj->localtax2_tx, $this->lines[$i]->fk_fournprice, $obj->pa_ht);
				//$this->lines[$i]->pa_ht				= $marginInfos[0];
				//$this->lines[$i]->marge_tx			= $marginInfos[1];
				//$this->lines[$i]->marque_tx			= $marginInfos[2];
				$this->lines[$i]->fk_parent_line	= $obj->fk_parent_line;
				$this->lines[$i]->special_code		= $obj->special_code;
				$this->lines[$i]->rang				= $obj->rang;

				$this->lines[$i]->ref_fourn				= $obj->ref_produit_fourn;

				// Multicurrency
				$this->lines[$i]->fk_multicurrency 			= $obj->fk_multicurrency;
				$this->lines[$i]->multicurrency_code 		= $obj->multicurrency_code;
				$this->lines[$i]->multicurrency_subprice 	= $obj->multicurrency_subprice;
				$this->lines[$i]->multicurrency_total_ht 	= $obj->multicurrency_total_ht;
				$this->lines[$i]->multicurrency_total_tva 	= $obj->multicurrency_total_tva;
				$this->lines[$i]->multicurrency_total_ttc 	= $obj->multicurrency_total_ttc;

				$i++;
			}
			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error=$this->db->error();
			return -1;
		}
	}
	/**
	 *  Create a document onto disk according to template module.
	 *
	 * 	@param	    string		$modele			Force model to use ('' to not force)
	 * 	@param		Translate	$outputlangs	Object langs to use for output
	 *  @param      int			$hidedetails    Hide details of lines
	 *  @param      int			$hidedesc       Hide description
	 *  @param      int			$hideref        Hide ref
	 * 	@return     int         				0 if KO, 1 if OK
	 */
	public function generateDocument($modele, $outputlangs, $hidedetails=0, $hidedesc=0, $hideref=0)
	{
		global $conf,$user,$langs;

		$langs->load("purchase");

		// Positionne le modele sur le nom du modele a utiliser
		if (! dol_strlen($modele))
		{
			if (! empty($conf->global->PURCHASE_SUPPLIER_ADDON_NUMBER_PDF))
			{
				$modele = $conf->global->PURCHASE_SUPPLIER_ADDON_NUMBER_PDF;
			}
			else
			{
				$modele = 'aurore';
			}
		}

		$modelpath = "purchase/core/modules/purchase/doc/";

		return $this->commonGenerateDocument($modelpath, $modele, $outputlangs, $hidedetails, $hidedesc, $hideref);
	}

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
		$dirtpls=array_merge($conf->modules_parts['tpl'],array('purchase/request/tpl'));
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
		global $conf, $hookmanager, $langs, $user,$objectdetadd;
		// TODO We should not use global var for this !
		global $inputalsopricewithtax, $usemargins, $disableedit, $disablemove, $disableremove;

		// Define usemargins
		$usemargins=0;
		if (! empty($conf->margin->enabled) && ! empty($this->element) && in_array($this->element,array('facture','propal','commande'))) $usemargins=1;

		print '<tr class="liste_titre nodrag nodrop">';

		if (! empty($conf->global->MAIN_VIEW_LINE_NUMBER)) print '<td class="linecolnum" align="center" width="5">&nbsp;</td>';

		// Description
		print '<td class="linecoldescription">'.$langs->trans('Description').'</td>';
		// Qty
		print '<td class="linecolqty" align="right">'.$langs->trans('Qty').'</td>';

		if($conf->global->PRODUCT_USE_UNITS)
		{
			print '<td class="linecoluseunit" align="left">'.$langs->trans('Unit').'</td>';
		}
		print '<td align="right" class="linecoledit">'.$langs->trans('Reference price').'</td>';
		print '<td align="right" class="linecoledit">'.$langs->trans('Total').'</td>';
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
		//$objtmp = new PurchaserequestLigneext($this->db);
		foreach ($this->lines as $line)
		{
			//$objtmp->fetch($line->id);
			//Line extrafield
			$line->fetch_optionals($line->id,$extralabelslines);

			//$resadd = $objectdetadd->fetch('',$line->id);
			//if ($resadd>0)
			//{
			//    $line->amount_ice = $objectdetadd->amount_ice;
			//    $line->discount = $objectdetadd->discount;
			//    $line->total_ttc = $line->total_ttc - $objectdetadd->discount;
			//}
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
					$reshook = $hookmanager->executeHooks('printObjectSubLine', $parameters, $this, $action);
					// Note that $action and $object may have been modified by some hooks
				}
			}
			if (empty($reshook))
			{
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
				$product_static->ref = $product_static->ref; //can change ref in hook
				$product_static->label = $product_static->label; //can change label in hook
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

			//$line->pu_ttc = price2num($line->subprice * (1 + ($line->tva_tx/100)), 'MU');
			//$line->pu_ttc = $line->price;
			// Output template part (modules that overwrite templates must declare this into descriptor)
			// Use global variables + $dateSelector + $seller and $buyer
			if ($this->status > 0)
			{
				$disableedit = true;
				$disableremove = true;
			}
			$dirtpls=array_merge($conf->modules_parts['tpl'],array('/purchase/request/tpl'));
			foreach($dirtpls as $reldir)
			{
				$tpl = dol_buildpath($reldir.'/objectline_view.tpl.php');
				if (empty($conf->file->strict_mode)) {
					$res=@include $tpl;
				} else {
					$res=include $tpl;
					// for debug
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
			$line->pu_ttc = $line->price;
			//$line->pu_ttc = price2num($line->subprice * (1 + ($line->tva_tx/100)), 'MU');

			// Output template part (modules that overwrite templates must declare this into descriptor)
			// Use global variables + $dateSelector + $seller and $buyer
			$dirtpls=array_merge($conf->modules_parts['tpl'],array('/purchase/request/tpl'));
			foreach($dirtpls as $reldir)
			{
				$tpl = dol_buildpath($reldir.'/objectline_edit.tpl.php');
				if (empty($conf->file->strict_mode)) {
					$res=@include $tpl;
				} else {
					$res=include $tpl;
					 // for debug
				}
				if ($res) break;
			}
		}
	}

	/**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatutprocess($mode=0)
	{
		return $this->LibStatutprocess($this->status_process,$mode);
	}

	/**
	 *  Return the status
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 5=Long label + Picto
	 *  @return string 			       	Label of status
	 */
	static function LibStatutprocess($status,$mode=0)
	{
		global $langs;

		if ($mode == 0)
		{
			$prefix='';
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Draft');
		}
		if ($mode == 1)
		{
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Draft');
		}
		if ($mode == 2)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut5').' '.$langs->trans('Draft');
		}
		if ($mode == 3)
		{
			if(is_null($status)) return img_picto($langs->trans('Draft'),DOL_URL_ROOT.'/poa/img/pen','',1);
			elseif ($status == 0) return img_picto($langs->trans('Preventive'),DOL_URL_ROOT.'/poa/img/pre','',1);
			elseif ($status == 1) return img_picto($langs->trans('Processstarted'),DOL_URL_ROOT.'/poa/img/process','',1);
			elseif ($status == 2) return img_picto($langs->trans('Proposalselection'),DOL_URL_ROOT.'/poa/img/selprop','',1);
			elseif ($status == 3) return img_picto($langs->trans('Commited'),DOL_URL_ROOT.'/poa/img/com','',1);
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut5').' '.$langs->trans('Draft');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Draft').' '.img_picto($langs->trans('Draft'),'statut5');
		}
		if ($mode == 6)
		{
			if(is_null($status)) return $langs->trans('Draft').' '.img_picto($langs->trans('Draft'),DOL_URL_ROOT.'/poa/img/pen','',1);
			elseif ($status == 0) return $langs->trans('Preventive').' '.img_picto($langs->trans('Preventive'),DOL_URL_ROOT.'/poa/img/pre','',1);
			elseif ($status == 1) return $langs->trans('Processstarted').' '.img_picto($langs->trans('Processstarted'),DOL_URL_ROOT.'/poa/img/process','',1);
			elseif ($status == 2) return $langs->trans('Proposalselection').' '.img_picto($langs->trans('Proposalselection'),DOL_URL_ROOT.'/poa/img/selprop','',1);
			elseif ($status == 3) return $langs->trans('Commited').' '.img_picto($langs->trans('Commited'),DOL_URL_ROOT.'/poa/img/com','',1);
		}
	}

	/**
	 * 	Return HTML table table of source object lines
	 *  TODO Move this and previous function into output html class file (htmlline.class.php).
	 *  If lines are into a template, title must also be into a template
	 *  But for the moment we don't know if it's possible as we keep a method available on overloaded objects.
	 *
	 *  @return	void
	 */
	function printOriginLinesList()
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
		print '<td align="right">'.$langs->trans('ReductionShort').'</td>';
		if ($conf->global->PURCHASE_INTEGRATED_POA && $conf->poa->enabled && $this->fk_type_adj !=3)
		{
			print '<td align="right">'.$langs->trans('Select').'</td>';
		}
		print '</tr>';
		$var = true;
		$i	 = 0;

		foreach ($this->lines as $line)
		{
			$lView = true;
			if ($conf->global->PURCHASE_INTEGRATED_POA && $conf->poa->enabled)
			{
				if (!empty($line->fk_commande_fournisseurdet)) $lView = false;
			}
			if ($lView)
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

		if (($line->info_bits & 2) == 2)
		// TODO Not sure this is used for source object
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
		if ($this->fk_type_adj)
		{
			$this->tpl['select'] = '<input type="checkbox" name="selreg['.$line->id.']">';
		}
		// Output template part (modules that overwrite templates must declare this into descriptor)
		// Use global variables + $dateSelector + $seller and $buyer
		$dirtpls=array_merge($conf->modules_parts['tpl'],array('/purchase/core/tpl'));
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
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatutpurchase($mode=0)
	{
		return $this->LibStatutpurchase($this->status_purchase,$mode);
	}

	/**
	 *  Return the status
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 5=Long label + Picto
	 *  @return string 			       	Label of status
	 */
	static function LibStatutpurchase($status,$mode=0)
	{
		global $langs;

		if ($mode == 0)
		{
			$prefix='';
			if ($status == -2) return $langs->trans('Cancelled');
			if ($status == -1) return $langs->trans('Rejected');
			if ($status == 1) return $langs->trans('Served');
			if ($status == 0) return $langs->trans('Pending');
		}
		if ($mode == 1)
		{
			if ($status == -2) return $langs->trans('Cancelled');
			if ($status == -1) return $langs->trans('Rejected');
			if ($status == 1) return $langs->trans('Served');
			if ($status == 0) return $langs->trans('Pending');
		}
		if ($mode == 2)
		{
			if ($status == -2) return img_picto($langs->trans('Cancelled'),'statut8').' '.$langs->trans('Cancelled');
			if ($status == -1) return img_picto($langs->trans('Rejected'),'statut9').' '.$langs->trans('Rejected');
			if ($status == 1) return img_picto($langs->trans('Served'),'statut1').' '.$langs->trans('Served');
			if ($status == 0) return img_picto($langs->trans('Pending'),'statut0').' '.$langs->trans('Pending');
		}
		if ($mode == 3)
		{
			if ($status == -2) return img_picto($langs->trans('Cancelled'),'statut8');
			if ($status == -1) return img_picto($langs->trans('Rejected'),'statut9');
			if ($status == 1) return img_picto($langs->trans('Served'),'statut1');
			if ($status == 0) return img_picto($langs->trans('Pending'),'statut0');
		}
		if ($mode == 4)
		{
			if ($status == -2) return img_picto($langs->trans('Cancelled'),'statut8').' '.$langs->trans('Cancelled');
			if ($status == -1) return img_picto($langs->trans('Rejected'),'statut9').' '.$langs->trans('Rejected');
			if ($status == 1) return img_picto($langs->trans('Served'),'statut1').' '.$langs->trans('Served');
			if ($status == 0) return img_picto($langs->trans('Pending'),'statut0').' '.$langs->trans('Pending');
		}
		if ($mode == 5)
		{
			if ($status == -2) return $langs->trans('Cancelled').' '.img_picto($langs->trans('Cancelled'),'statut8');
			if ($status == -1) return $langs->trans('Rejected').' '.img_picto($langs->trans('Rejected'),'statut9');
			if ($status == 1) return $langs->trans('Served').' '.img_picto($langs->trans('Served'),'statut1');
			if ($status == 0) return $langs->trans('Pending').' '.img_picto($langs->trans('Pending'),'statut0');
		}
		if ($mode == 6)
		{
			if ($status == -2) return $langs->trans('Cancelled').' '.img_picto($langs->trans('Cancelled'),'statut8');
			if ($status == -1) return $langs->trans('Rejected').' '.img_picto($langs->trans('Rejected'),'statut9');
			if ($status == 1) return $langs->trans('Served').' '.img_picto($langs->trans('Served'),'statut1');
			if ($status == 0) return $langs->trans('Pending').' '.img_picto($langs->trans('Pending'),'statut0');
		}
	}
}

class PurchaserequestLineext extends PurchaserequestLine
{
		/**
	 * 	Class line Contructor
	 *
	 * 	@param	DoliDB	$db	Database handler
	 */
		function __construct($db)
		{
			$this->db= $db;
		}

	/**
	 *	Retrieve the propal line object
	 *
	 *	@param	int		$rowid		Propal line id
	 *	@return	int					<0 if KO, >0 if OK
	 */
	function fetchxxxx($rowid)
	{
		$sql = 'SELECT pd.rowid, pd.fk_purchase_request, pd.fk_parent_line, pd.fk_product, pd.label as custom_label, pd.description, pd.price, pd.qty, pd.tva_tx,';
		$sql.= ' pd.remise, pd.remise_percent, pd.fk_remise_except, pd.subprice,';
		$sql.= ' pd.info_bits, pd.total_ht, pd.total_tva, pd.total_ttc, pd.fk_product_fournisseur_price as fk_fournprice, pd.buy_price_ht as pa_ht, pd.special_code, pd.rang,';
		$sql.= ' pd.localtax1_tx, pd.localtax2_tx, pd.total_localtax1, pd.total_localtax2,';
		$sql.= ' p.ref as product_ref, p.label as product_label, p.description as product_desc,';
		$sql.= ' pd.product_type, pd.ref_fourn as ref_produit_fourn,';
		$sql.= ' pd.fk_multicurrency, pd.multicurrency_code, pd.multicurrency_subprice, pd.multicurrency_total_ht, pd.multicurrency_total_tva, pd.multicurrency_total_ttc';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'purchase_requestdet as pd';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON pd.fk_product = p.rowid';
		$sql.= ' WHERE pd.rowid = '.$rowid;

		$result = $this->db->query($sql);
		if ($result)
		{
			$objp = $this->db->fetch_object($result);

			$this->rowid			= $objp->rowid; // deprecated
			$this->id				= $objp->rowid;
			$this->fk_purchase_request		= $objp->fk_purchase_request;
			$this->fk_parent_line	= $objp->fk_parent_line;
			$this->label			= $objp->custom_label;
			$this->desc				= $objp->description;
			$this->qty				= $objp->qty;
			$this->price			= $objp->price;		// deprecated
			$this->subprice			= $objp->subprice;
			$this->tva_tx			= $objp->tva_tx;
			$this->remise			= $objp->remise;
			$this->remise_percent	= $objp->remise_percent;
			$this->fk_remise_except = $objp->fk_remise_except;
			$this->fk_product		= $objp->fk_product;
			$this->info_bits		= $objp->info_bits;

			$this->total_ht			= $objp->total_ht;
			$this->total_tva		= $objp->total_tva;
			$this->total_ttc		= $objp->total_ttc;

			$this->fk_fournprice	= $objp->fk_fournprice;

			$marginInfos			= getMarginInfos($objp->subprice, $objp->remise_percent, $objp->tva_tx, $objp->localtax1_tx, $objp->localtax2_tx, $this->fk_fournprice, $objp->pa_ht);
			$this->pa_ht			= $marginInfos[0];
			$this->marge_tx			= $marginInfos[1];
			$this->marque_tx		= $marginInfos[2];

			$this->special_code		= $objp->special_code;
			$this->product_type		= $objp->product_type;
			$this->rang				= $objp->rang;

			$this->ref				= $objp->product_ref;      // deprecated
			$this->product_ref		= $objp->product_ref;
			$this->libelle			= $objp->product_label;  // deprecated
			$this->product_label	= $objp->product_label;
			$this->product_desc		= $objp->product_desc;

			$this->ref_fourn		= $objp->ref_produit_forun;

			// Multicurrency
			$this->fk_multicurrency 		= $objp->fk_multicurrency;
			$this->multicurrency_code 		= $objp->multicurrency_code;
			$this->multicurrency_subprice 	= $objp->multicurrency_subprice;
			$this->multicurrency_total_ht 	= $objp->multicurrency_total_ht;
			$this->multicurrency_total_tva 	= $objp->multicurrency_total_tva;
			$this->multicurrency_total_ttc 	= $objp->multicurrency_total_ttc;

			$this->db->free($result);
		}
		else
		{
			dol_print_error($this->db);
		}
	}

	/**
	 *  Insert object line propal in database
	 *
	 *	@param		int		$notrigger		1=Does not execute triggers, 0= execuete triggers
	 *	@return		int						<0 if KO, >0 if OK
	 */
	function insert($notrigger=0)
	{
		global $conf,$langs,$user;

		$error=0;

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
		if (empty($this->fk_fournprice)) $this->fk_fournprice=0;

		if (empty($this->pa_ht)) $this->pa_ht=0;

		// if buy price not defined, define buyprice as configured in margin admin
		if ($this->pa_ht == 0)
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

		// Insert line into database
		$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'purchase_requestdet';
		$sql.= ' (fk_purchase_request, fk_parent_line, label, description, fk_product, product_type,';
		$sql.= ' fk_remise_except, qty, tva_tx, localtax1_tx, localtax2_tx, localtax1_type, localtax2_type,';
		$sql.= ' subprice, remise_percent, ';
		$sql.= ' info_bits, ';
		$sql.= ' total_ht, total_tva, total_localtax1, total_localtax2, total_ttc, fk_product_fournisseur_price, buy_price_ht, special_code, rang,';
		$sql.= ' ref_fourn';
		$sql.= ', fk_multicurrency, multicurrency_code, multicurrency_subprice, multicurrency_total_ht, multicurrency_total_tva, multicurrency_total_ttc)';
		$sql.= " VALUES (".$this->fk_purchase_request.",";
		$sql.= " ".($this->fk_parent_line>0?"'".$this->fk_parent_line."'":"null").",";
		$sql.= " ".(! empty($this->label)?"'".$this->db->escape($this->label)."'":"null").",";
		$sql.= " '".$this->db->escape($this->desc)."',";
		$sql.= " ".($this->fk_product?"'".$this->fk_product."'":"null").",";
		$sql.= " '".$this->product_type."',";
		$sql.= " ".($this->fk_remise_except?"'".$this->fk_remise_except."'":"null").",";
		$sql.= " ".price2num($this->qty).",";
		$sql.= " ".price2num($this->tva_tx).",";
		$sql.= " ".price2num($this->localtax1_tx).",";
		$sql.= " ".price2num($this->localtax2_tx).",";
		$sql.= " '".$this->localtax1_type."',";
		$sql.= " '".$this->localtax2_type."',";
		$sql.= " ".($this->subprice?price2num($this->subprice):"null").",";
		$sql.= " ".price2num($this->remise_percent).",";
		$sql.= " ".(isset($this->info_bits)?"'".$this->info_bits."'":"null").",";
		$sql.= " ".price2num($this->total_ht).",";
		$sql.= " ".price2num($this->total_tva).",";
		$sql.= " ".price2num($this->total_localtax1).",";
		$sql.= " ".price2num($this->total_localtax2).",";
		$sql.= " ".price2num($this->total_ttc).",";
		$sql.= " ".(!empty($this->fk_fournprice)?"'".$this->fk_fournprice."'":"null").",";
		$sql.= " ".(isset($this->pa_ht)?"'".price2num($this->pa_ht)."'":"null").",";
		$sql.= ' '.$this->special_code.',';
		$sql.= ' '.$this->rang.',';
		$sql.= " '".$this->db->escape($this->ref_fourn)."'";
		$sql.= ", ".($this->fk_multicurrency > 0?$this->fk_multicurrency:'null');
		$sql.= ", '".$this->db->escape($this->multicurrency_code)."'";
		$sql.= ", ".$this->multicurrency_subprice;
		$sql.= ", ".$this->multicurrency_total_ht;
		$sql.= ", ".$this->multicurrency_total_tva;
		$sql.= ", ".$this->multicurrency_total_ttc;
		$sql.= ')';

		dol_syslog(get_class($this).'::insert', LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$this->rowid=$this->db->last_insert_id(MAIN_DB_PREFIX.'purchase_requestdet');
			$this->id=$this->rowid;

			if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED)) // For avoid conflicts if trigger used
			{
				$result=$this->insertExtraFields();
				if ($result < 0)
				{
					$error++;
				}
			}

			if (! $notrigger)
			{
				// Call trigger
				$result=$this->call_trigger('LINEPURCHASE_REQUEST_INSERT',$user);
				if ($result < 0)
				{
					$this->db->rollback();
					return -1;
				}
				// End call triggers
			}

			$this->db->commit();
			return 1;
		}
		else
		{
			$this->error=$this->db->error()." sql=".$sql;
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 * 	Delete line in database
	 *
	 *	@return	 int  <0 if ko, >0 if ok
	 */
	function delete()
	{
		global $conf,$langs,$user;

		$error=0;
		$this->db->begin();

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."purchase_requestdet WHERE rowid = ".$this->rowid;
		dol_syslog("PurchaserequestLine::delete", LOG_DEBUG);
		if ($this->db->query($sql) )
		{

			// Remove extrafields
			if ((! $error) && (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED))) // For avoid conflicts if trigger used
			{
				$this->id=$this->rowid;
				$result=$this->deleteExtraFields();
				if ($result < 0)
				{
					$error++;
					dol_syslog(get_class($this)."::delete error -4 ".$this->error, LOG_ERR);
				}
			}

			// Call trigger
			$result=$this->call_trigger('LINEPURCHASE_REQUEST_DELETE',$user);
			if ($result < 0)
			{
				$this->db->rollback();
				return -1;
			}
			// End call triggers

			$this->db->commit();

			return 1;
		}
		else
		{
			$this->error=$this->db->error()." sql=".$sql;
			$this->db->rollback();
			return -1;
		}
	}

	/**
	 *	Update propal line object into DB
	 *
	 *	@param 	int		$notrigger	1=Does not execute triggers, 0= execuete triggers
	 *	@return	int					<0 if ko, >0 if ok
	 */
	function update($notrigger=0)
	{
		global $conf,$langs,$user;

		$error=0;

		// Clean parameters
		if (empty($this->tva_tx)) $this->tva_tx=0;
		if (empty($this->localtax1_tx)) $this->localtax1_tx=0;
		if (empty($this->localtax2_tx)) $this->localtax2_tx=0;
		if (empty($this->total_localtax1)) $this->total_localtax1=0;
		if (empty($this->total_localtax2)) $this->total_localtax2=0;
		if (empty($this->localtax1_type)) $this->localtax1_type=0;
		if (empty($this->localtax2_type)) $this->localtax2_type=0;
		if (empty($this->marque_tx)) $this->marque_tx=0;
		if (empty($this->marge_tx)) $this->marge_tx=0;
		if (empty($this->price)) $this->price=0;	// TODO A virer
		if (empty($this->remise)) $this->remise=0;	// TODO A virer
		if (empty($this->remise_percent)) $this->remise_percent=0;
		if (empty($this->info_bits)) $this->info_bits=0;
		if (empty($this->special_code)) $this->special_code=0;
		if (empty($this->fk_parent_line)) $this->fk_parent_line=0;
		if (empty($this->fk_fournprice)) $this->fk_fournprice=0;

		if (empty($this->pa_ht)) $this->pa_ht=0;

		// if buy price not defined, define buyprice as configured in margin admin
		if ($this->pa_ht == 0)
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
		$sql = "UPDATE ".MAIN_DB_PREFIX."purchase_requestdet SET";
		$sql.= " description='".$this->db->escape($this->desc)."'";
		$sql.= " , label=".(! empty($this->label)?"'".$this->db->escape($this->label)."'":"null");
		$sql.= " , product_type=".$this->product_type;
		$sql.= " , tva_tx='".price2num($this->tva_tx)."'";
		$sql.= " , localtax1_tx=".price2num($this->localtax1_tx);
		$sql.= " , localtax2_tx=".price2num($this->localtax2_tx);
		$sql.= " , localtax1_type='".$this->localtax1_type."'";
		$sql.= " , localtax2_type='".$this->localtax2_type."'";
		$sql.= " , qty='".price2num($this->qty)."'";
		$sql.= " , subprice=".price2num($this->subprice)."";
		$sql.= " , remise_percent=".price2num($this->remise_percent)."";
		$sql.= " , price=".price2num($this->price)."";					// TODO A virer
		$sql.= " , remise=".price2num($this->remise)."";				// TODO A virer
		$sql.= " , info_bits='".$this->info_bits."'";
		if (empty($this->skip_update_total))
		{
			$sql.= " , total_ht=".price2num($this->total_ht)."";
			$sql.= " , total_tva=".price2num($this->total_tva)."";
			$sql.= " , total_ttc=".price2num($this->total_ttc)."";
			$sql.= " , total_localtax1=".price2num($this->total_localtax1)."";
			$sql.= " , total_localtax2=".price2num($this->total_localtax2)."";
		}
		$sql.= " , fk_product_fournisseur_price=".(! empty($this->fk_fournprice)?"'".$this->fk_fournprice."'":"null");
		$sql.= " , buy_price_ht=".price2num($this->pa_ht);
		if (strlen($this->special_code)) $sql.= " , special_code=".$this->special_code;
		$sql.= " , fk_parent_line=".($this->fk_parent_line>0?$this->fk_parent_line:"null");
		if (! empty($this->rang)) $sql.= ", rang=".$this->rang;
		$sql.= " , ref_fourn=".(! empty($this->ref_fourn)?"'".$this->db->escape($this->ref_fourn)."'":"null");

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
				$result=$this->call_trigger('LINEPURCHASE_REQUEST_UPDATE',$user);
				if ($result < 0)
				{
					$this->db->rollback();
					return -1;
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

	/**
	 *	Update DB line fields total_xxx
	 *	Used by migration
	 *
	 *	@return		int		<0 if ko, >0 if ok
	 */
	function update_total()
	{
		$this->db->begin();

		// Mise a jour ligne en base
		$sql = "UPDATE ".MAIN_DB_PREFIX."purchase_requestdet SET";
		$sql.= " total_ht=".price2num($this->total_ht,'MT')."";
		$sql.= ",total_tva=".price2num($this->total_tva,'MT')."";
		$sql.= ",total_ttc=".price2num($this->total_ttc,'MT')."";
		$sql.= " WHERE rowid = ".$this->rowid;

		dol_syslog("PurchaserequestLine::update_total", LOG_DEBUG);

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

class PurchaserequestLigneext extends CommonObjectLine
{
	var $product_ref;
	var $product_label;
	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		$this->db = $db;
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param int    $id  Id object
	 * @param string $ref Ref
	 *
	 * @return int <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetch($id, $ref = null)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';

		$sql .= " t.fk_purchase_request,";
		$sql .= " t.ref,";
		$sql .= " t.fk_parent_line,";
		$sql .= " t.fk_product,";
		$sql .= " t.label,";
		$sql .= " t.description,";
		$sql .= " t.qty,";
		$sql .= " t.fk_unit,";
		$sql .= " t.tva_tx,";
		$sql .= " t.subprice,";
		$sql .= " t.price,";
		$sql .= " t.total_ht,";
		$sql .= " t.total_ttc,";
		$sql .= " t.product_type,";
		$sql .= " t.info_bits,";
		$sql .= " t.special_code,";
		$sql .= " t.rang,";
		$sql .= " t.ref_fourn,";
		$sql .= " t.origin,";
		$sql .= " t.originid,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql .= " t.status";
		$sql .= " , p.ref AS product_ref ";
		$sql .= " , p.label AS product_label ";

		$sql .= ' FROM ' . MAIN_DB_PREFIX . 'purchase_requestdet' . ' as t';
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX.'product AS p ON t.fk_product = p.rowid';
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
			$sql .= " AND entity IN (" . getEntity("purchaserequestdet", 1) . ")";
		}
		if (null !== $ref) {
			$sql .= ' AND t.ref = ' . '\'' . $ref . '\'';
		} else {
			$sql .= ' AND t.rowid = ' . $id;
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;

				$this->fk_purchase_request = $obj->fk_purchase_request;
				$this->ref = $obj->ref;
				$this->fk_parent_line = $obj->fk_parent_line;
				$this->fk_product = $obj->fk_product;
				$this->label = $obj->label;
				$this->description = $obj->description;
				$this->qty = $obj->qty;
				$this->fk_unit = $obj->fk_unit;
				$this->tva_tx = $obj->tva_tx;
				$this->subprice = $obj->subprice;
				$this->price = $obj->price;
				$this->total_ht = $obj->total_ht;
				$this->total_ttc = $obj->total_ttc;
				$this->product_type = $obj->product_type;
				$this->product_ref = $obj->product_ref;
				$this->product_label = $obj->product_label;
				$this->info_bits = $obj->info_bits;
				$this->special_code = $obj->special_code;
				$this->rang = $obj->rang;
				$this->ref_fourn = $obj->ref_fourn;
				$this->origin = $obj->origin;
				$this->originid = $obj->originid;
				$this->fk_user_create = $obj->fk_user_create;
				$this->fk_user_mod = $obj->fk_user_mod;
				$this->datec = $this->db->jdate($obj->datec);
				$this->datem = $this->db->jdate($obj->datem);
				$this->tms = $this->db->jdate($obj->tms);
				$this->status = $obj->status;


			}

			// Retrieve all extrafields for invoice
			// fetch optionals attributes and labels
			require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
			$extrafields=new ExtraFields($this->db);
			$extralabels=$extrafields->fetch_name_optionals_label($this->table_element,true);
			$this->fetch_optionals($this->id,$extralabels);

			// $this->fetch_lines();

			$this->db->free($resql);

			if ($numrows) {
				return 1;
			} else {
				return 0;
			}
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}
}
?>