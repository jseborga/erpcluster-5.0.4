<?php
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.commande.class.php';
/* This is to show add lines */

class FournisseurCommandeext extends CommandeFournisseur
{

	var $aData;
	var $aDatadet;
	var $aDataid;
	var $linesdet;

	/**
	 * Save a receiving into the tracking table of receiving (commande_fournisseur_dispatch) and add product into stock warehouse.
	 *
	 * @param 	User		$user					User object making change
	 * @param 	int			$product				Id of product to dispatch
	 * @param 	double		$qty					Qty to dispatch
	 * @param 	int			$entrepot				Id of warehouse to add product
	 * @param 	double		$price					Unit Price for PMP value calculation (Unit price without Tax and taking into account discount)
	 * @param	string		$comment				Comment for stock movement
	 * @param	date		$eatby					eat-by date
	 * @param	date		$sellby					sell-by date
	 * @param	string		$batch					Lot number
	 * @param	int			$fk_commandefourndet	Id of supplier order line
	 * @param	int			$notrigger          	1 = notrigger
	 * @return 	int						<0 if KO, >0 if OK
	 */
	public function dispatchProductadd($user, $product, $qty, $entrepot, $price=0, $comment='', $eatby='', $sellby='', $batch='', $fk_commandefourndet=0, $notrigger=0)
	{
		global $conf, $langs;

		$error = 0;
		require_once DOL_DOCUMENT_ROOT .'/product/stock/class/mouvementstock.class.php';

		// Check parameters (if test are wrong here, there is bug into caller)
		if ($entrepot <= 0)
		{
			$this->error='ErrorBadValueForParameterWarehouse';
			return -1;
		}
		if ($qty <= 0)
		{
			$this->error='ErrorBadValueForParameterQty';
			return -1;
		}

		$dispatchstatus = 1;
		if (! empty($conf->global->SUPPLIER_ORDER_USE_DISPATCH_STATUS)) $dispatchstatus = 0;	// Setting dispatch status (a validation step after receiving products) will be done manually to 1 or 2 if this option is on

		$now=dol_now();

		if (($this->statut == 3 || $this->statut == 4 || $this->statut == 5))
		{
			$this->db->begin();

			$sql = "INSERT INTO ".MAIN_DB_PREFIX."commande_fournisseur_dispatch";
			$sql.= " (fk_commande, fk_product, qty, fk_entrepot, fk_user, datec, fk_commandefourndet, status, comment, eatby, sellby, batch) VALUES";
			$sql.= " ('".$this->id."','".$product."','".$qty."',".($entrepot>0?"'".$entrepot."'":"null").",'".$user->id."','".$this->db->idate($now)."','".$fk_commandefourndet."', ".$dispatchstatus.", '".$this->db->escape($comment)."', ";
			$sql.= ($eatby?"'".$this->db->idate($eatby)."'":"null").", ".($sellby?"'".$this->db->idate($sellby)."'":"null").", ".($batch?"'".$batch."'":"null");
			$sql.= ")";

			dol_syslog(get_class($this)."::dispatchProduct", LOG_DEBUG);
			$resql = $this->db->query($sql);
			if ($resql)
			{
				//registro adicionado para enviar el id creado al triggerr
				$this->idreg = $this->db->last_insert_id(MAIN_DB_PREFIX . $this->table_element);
				if (! $notrigger)
				{
					global $conf, $langs, $user;
					// Call trigger
					$result=$this->call_trigger('LINEORDER_SUPPLIER_DISPATCH',$user);
					if ($result < 0)
					{
						$error++;
						return -1;
					}
					// End call triggers
				}
			}
			else
			{
				$this->error=$this->db->lasterror();
				$error++;
			}

			// Si module stock gere et que incrementation faite depuis un dispatching en stock
			if (! $error && $entrepot > 0 && ! empty($conf->stock->enabled) && ! empty($conf->global->STOCK_CALCULATE_ON_SUPPLIER_DISPATCH_ORDER))
			{

				$mouv = new MouvementStock($this->db);
				if ($product > 0)
				{
					// $price should take into account discount (except if option STOCK_EXCLUDE_DISCOUNT_FOR_PMP is on)
					$mouv->origin = &$this;
					$result=$mouv->reception($user, $product, $entrepot, $qty, $price, $comment, $eatby, $sellby, $batch);
					if ($result < 0)
					{
						$this->error=$mouv->error;
						$this->errors=$mouv->errors;
						dol_syslog(get_class($this)."::dispatchProduct ".$this->error." ".join(',',$this->errors), LOG_ERR);
						$error++;
					}
				}
			}

			if ($error == 0)
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
			$this->error='BadStatusForObject';
			return -2;
		}
	}


	/**
	 *  Mise a jour de l'objet ligne de commande en base
	 *
	 *  @return     int     <0 si ko, >0 si ok
	 */
	function update_total()
	{
		$this->db->begin();

		// Mise a jour ligne en base
		$sql = "UPDATE ".MAIN_DB_PREFIX."commande_fournisseur SET";
		$sql.= " total_ht='".price2num($this->total_ht)."'";
		$sql.= ",tva='".price2num($this->tva)."'";
		$sql.= ",localtax1='".price2num($this->total_localtax1)."'";
		$sql.= ",localtax2='".price2num($this->total_localtax2)."'";
		$sql.= ",total_ttc='".price2num($this->total_ttc)."'";
		$sql.= " WHERE rowid = ".$this->id;

		dol_syslog("FournisseurCommandeext.class.php::update_total", LOG_DEBUG);

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
	 *	Return clicable name (with picto eventually)
	 *
	 *	@param		int		$withpicto		0=No picto, 1=Include picto into link, 2=Only picto
	 *	@param		string	$option			On what the link points
	 *	@return		string					Chain with URL
	 */
	function getNomUrladd($withpicto=0,$option='')
	{
		global $langs, $conf;

		$result='';
		$label = '<u>' . $langs->trans("ShowOrder") . '</u>';
		if (! empty($this->ref))
			$label .= '<br><b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;
		if (! empty($this->ref_supplier))
			$label.= '<br><b>' . $langs->trans('RefSupplier') . ':</b> ' . $this->ref_supplier;
		if (! empty($this->total_ht))
			$label.= '<br><b>' . $langs->trans('AmountHT') . ':</b> ' . price($this->total_ht, 0, $langs, 0, -1, -1, $conf->currency);
		if (! empty($this->total_tva))
			$label.= '<br><b>' . $langs->trans('VAT') . ':</b> ' . price($this->total_tva, 0, $langs, 0, -1, -1, $conf->currency);
		if (! empty($this->total_ttc))
			$label.= '<br><b>' . $langs->trans('AmountTTC') . ':</b> ' . price($this->total_ttc, 0, $langs, 0, -1, -1, $conf->currency);

		$link = '<a href="'.DOL_URL_ROOT.'/purchase/commande/card.php?id='.$this->id.'" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip">';
		$linkend='</a>';

		$picto='order';

		if ($withpicto) $result.=($link.img_object($label, $picto, 'class="classfortooltip"').$linkend);
		if ($withpicto && $withpicto != 2) $result.=' ';
		$result.=$link.$this->ref.$linkend;
		return $result;
	}

	//funcion para sumar los impuestos registrados
	//se debe considerar que aparte del IVA los otros impuestos
	//se guardaran en localtax1, localtax2, localtax3 etc
	function get_sum_taxes($id)
	{
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_commande,";
		$sql.= " t.tva_tx,";
		$sql.= " t.tva_tx,";
		$sql.= " t.localtax1_tx,";
		$sql.= " t.localtax1_type,";
		$sql.= " t.localtax2_tx,";
		$sql.= " t.localtax2_type,";
		$sql.= " t.remise,";
		$sql.= " t.total_ht,";
		$sql.= " t.total_tva,";
		$sql.= " t.total_localtax1,";
		$sql.= " t.total_localtax2,";
		$sql.= " t.total_ttc";

		$sql.= " FROM ".MAIN_DB_PREFIX."commande_fournisseurdet as t";
		$sql.= " WHERE t.fk_commande = ".$id;

		dol_syslog(get_class($this)."::get_sum_taxes sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->aData = array();
		$this->aDataid = array();
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$i = 0;
				$num = $this->db->num_rows($resql);
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$this->aDataid[$obj->fk_commande] = $obj->fk_commande;
					if (!empty($obj->tva_tx))
						$this->aData['tva_tx'] = $obj->tva_tx;
					$this->aData['total_tva']+= $obj->total_tva;
					$this->aData['total_localtax1']+= $obj->total_localtax1;
					$this->aData['total_localtax2']+= $obj->total_localtax2;
					$this->aData['total_ttc']+= $obj->total_ttc;
					$this->aData['total_ht']+= $obj->total_ht;
					$i++;
				}

				return $num;
			}
			else
				return 0;
		}
		return -1;
	}
	/**
	 *  Get object and lines from database
	 *
	 *  @param  int     $id         Id of order to load
	 *  @param  string  $ref        Ref of object
	 *  @return int                 >0 if OK, <0 if KO, 0 if not found
	 */
	function fetch_($id,$ref='')
	{
		global $conf;

		// Check parameters
		if (empty($id) && empty($ref)) return -1;

		$sql = "SELECT c.rowid, c.ref, ref_supplier, c.fk_soc, c.fk_statut, c.amount_ht, c.total_ht, c.total_ttc, c.tva,";
		$sql.= " c.localtax1, c.localtax2, ";
		$sql.= " c.date_creation, c.date_valid, c.date_approve, c.date_approve2,";
		$sql.= " c.fk_user_author, c.fk_user_valid, c.fk_user_approve, c.fk_user_approve2,";
		$sql.= " c.date_commande as date_commande, c.date_livraison as date_livraison, c.fk_cond_reglement, c.fk_mode_reglement, c.fk_projet as fk_project, c.remise_percent, c.source, c.fk_input_method,";
		$sql.= " c.fk_account,";
		$sql.= " c.note_private, c.note_public, c.model_pdf, c.extraparams, c.billed,";
		$sql.= " c.fk_multicurrency, c.multicurrency_code, c.multicurrency_tx, c.multicurrency_total_ht, c.multicurrency_total_tva, c.multicurrency_total_ttc,";
		$sql.= " cm.libelle as methode_commande,";
		$sql.= " cr.code as cond_reglement_code, cr.libelle as cond_reglement_libelle,";
		$sql.= " p.code as mode_reglement_code, p.libelle as mode_reglement_libelle";
		$sql.= ', c.fk_incoterms, c.location_incoterms';
		$sql.= ', i.libelle as libelle_incoterms';
		$sql.= ', ca.fk_departament, ca.ref_contrat, ca.term, ca.ref_term, ca.type AS type_add ';
		$sql.= " FROM ".MAIN_DB_PREFIX."commande_fournisseur as c";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."commande_fournisseur_add as ca ON (ca.fk_commande_fournisseur = c.rowid)";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_payment_term as cr ON (c.fk_cond_reglement = cr.rowid)";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_paiement as p ON (c.fk_mode_reglement = p.id)";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_input_method as cm ON cm.rowid = c.fk_input_method";
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_incoterms as i ON c.fk_incoterms = i.rowid';
		$sql.= " WHERE c.entity = ".$conf->entity;
		if ($ref) $sql.= " AND c.ref='".$this->db->escape($ref)."'";
		else $sql.= " AND c.rowid=".$id;

		dol_syslog(get_class($this)."::fetch", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql)
		{
			$obj = $this->db->fetch_object($resql);
			if (! $obj)
			{
				$this->error='Bill with id '.$id.' not found';
				dol_syslog(get_class($this).'::fetch '.$this->error);
				return 0;
			}

			$this->id                   = $obj->rowid;
			$this->ref                  = $obj->ref;
			$this->ref_supplier         = $obj->ref_supplier;
			$this->socid                = $obj->fk_soc;
			$this->fourn_id             = $obj->fk_soc;
			$this->statut               = $obj->fk_statut;
			$this->billed               = $obj->billed;
			$this->user_author_id       = $obj->fk_user_author;
			$this->user_valid_id        = $obj->fk_user_valid;
			$this->user_approve_id      = $obj->fk_user_approve;
			$this->user_approve_id2     = $obj->fk_user_approve2;
			$this->total_ht             = $obj->total_ht;
			$this->total_tva            = $obj->tva;
			$this->total_localtax1      = $obj->localtax1;
			$this->total_localtax2      = $obj->localtax2;
			$this->total_ttc            = $obj->total_ttc;
			$this->date                 = $this->db->jdate($obj->date_creation);
			$this->date_valid           = $this->db->jdate($obj->date_valid);
			$this->date_approve         = $this->db->jdate($obj->date_approve);
			$this->date_approve2        = $this->db->jdate($obj->date_approve2);
			$this->date_commande        = $this->db->jdate($obj->date_commande); // date we make the order to supplier
			$this->date_livraison       = $this->db->jdate($obj->date_livraison);
			$this->remise_percent       = $obj->remise_percent;
			$this->methode_commande_id  = $obj->fk_input_method;
			$this->methode_commande     = $obj->methode_commande;

			$this->source               = $obj->source;
			$this->fk_project           = $obj->fk_project;
			$this->cond_reglement_id    = $obj->fk_cond_reglement;
			$this->cond_reglement_code  = $obj->cond_reglement_code;
			$this->cond_reglement       = $obj->cond_reglement_libelle;
			$this->cond_reglement_doc   = $obj->cond_reglement_libelle;
			$this->fk_account           = $obj->fk_account;
			$this->mode_reglement_id    = $obj->fk_mode_reglement;
			$this->mode_reglement_code  = $obj->mode_reglement_code;
			$this->mode_reglement       = $obj->mode_reglement_libelle;
			$this->note                 = $obj->note_private;    // deprecated
			$this->note_private         = $obj->note_private;
			$this->note_public          = $obj->note_public;
			$this->modelpdf             = $obj->model_pdf;

			//Incoterms
			$this->fk_incoterms = $obj->fk_incoterms;
			$this->location_incoterms = $obj->location_incoterms;
			$this->libelle_incoterms = $obj->libelle_incoterms;

			// Multicurrency
			$this->fk_multicurrency         = $obj->fk_multicurrency;
			$this->multicurrency_code       = $obj->multicurrency_code;
			$this->multicurrency_tx         = $obj->multicurrency_tx;
			$this->multicurrency_total_ht   = $obj->multicurrency_total_ht;
			$this->multicurrency_total_tva  = $obj->multicurrency_total_tva;
			$this->multicurrency_total_ttc  = $obj->multicurrency_total_ttc;

			//add
			$this->fk_departament = $obj->fk_departament;
			$this->ref_contrat = $obj->ref_contrat;
			$this->term = $obj->term;
			$this->ref_term = $obj->ref_term;
			$this->type_add = $obj->type_add;



			$this->extraparams          = (array) json_decode($obj->extraparams, true);

			$this->db->free($resql);

			// Retrieve all extrafields
			// fetch optionals attributes and labels
			require_once(DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php');
			$extrafields=new ExtraFields($this->db);
			$extralabels=$extrafields->fetch_name_optionals_label($this->table_element,true);
			$this->fetch_optionals($this->id,$extralabels);

			if ($this->statut == 0) $this->brouillon = 1;

			$this->fetchObjectLinked();

			$this->lines=array();

			$sql = "SELECT l.rowid, l.ref as ref_supplier, l.fk_product, l.product_type, l.label, l.description,";
			$sql.= " l.qty,";
			$sql.= " l.tva_tx, l.remise_percent, l.subprice,";
			$sql.= " l.localtax1_tx, l. localtax2_tx, l.total_localtax1, l.total_localtax2,";
			$sql.= " l.total_ht, l.total_tva, l.total_ttc, l.special_code, l.fk_parent_line, l.rang,";
			$sql.= " p.rowid as product_id, p.ref as product_ref, p.label as product_label, p.description as product_desc,";
			$sql.= " l.fk_unit, l.price,";
			$sql.= " l.date_start, l.date_end,";
			$sql.= " l.fk_multicurrency, l.multicurrency_code, l.multicurrency_subprice, l.multicurrency_total_ht, ";
			$sql.= " l.multicurrency_total_tva, l.multicurrency_total_ttc, ";
			$sql.= " la.fk_object, la.object, la.fk_fabrication, la.fk_fabricationdet, la.fk_projet, la.fk_projet_task, ";
			$sql.= " la.fk_jobs, la.fk_jobsdet, la.fk_structure, la.fk_poa, la.partida, la.amount_ice, la.discount ";
			$sql.= " FROM ".MAIN_DB_PREFIX."commande_fournisseurdet as l ";
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."commande_fournisseurdet_add as la ON la.fk_commande_fournisseurdet = l.rowid ";
			$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON l.fk_product = p.rowid';
			$sql.= " WHERE l.fk_commande = ".$this->id;
			$sql.= " ORDER BY l.rang, l.rowid";
			//print $sql;

			dol_syslog(get_class($this)."::fetch get lines", LOG_DEBUG);
			$result = $this->db->query($sql);
			if ($result)
			{
				$num = $this->db->num_rows($result);
				$i = 0;

				while ($i < $num)
				{
					$objp                  = $this->db->fetch_object($result);

					$line                 = new CommandeFournisseurLigne($this->db);

					$line->id                  = $objp->rowid;
					$line->desc                = $objp->description;
					$line->description         = $objp->description;
					$line->qty                 = $objp->qty;
					$line->tva_tx              = $objp->tva_tx;
					$line->localtax1_tx        = $objp->localtax1_tx;
					$line->localtax2_tx        = $objp->localtax2_tx;
					$line->subprice            = $objp->subprice;
					$line->pu_ht               = $objp->subprice;
					$line->price               = $objp->price;
					$line->pu_ttc              = $objp->price;
					$line->remise_percent      = $objp->remise_percent;
					$line->total_ht            = $objp->total_ht;
					$line->total_tva           = $objp->total_tva;
					$line->total_localtax1     = $objp->total_localtax1;
					$line->total_localtax2     = $objp->total_localtax2;
					$line->total_ttc           = $objp->total_ttc;
					$line->product_type        = $objp->product_type;

					$line->fk_product          = $objp->fk_product;

					$line->libelle             = $objp->product_label;
					$line->product_label       = $objp->product_label;
					$line->product_desc        = $objp->product_desc;

					$line->ref                 = $objp->product_ref;
					$line->product_ref         = $objp->product_ref;
					$line->ref_fourn           = $objp->ref_supplier;
					$line->ref_supplier        = $objp->ref_supplier;

					$line->date_start          = $this->db->jdate($objp->date_start);
					$line->date_end            = $this->db->jdate($objp->date_end);
					$line->fk_unit             = $objp->fk_unit;

					// Multicurrency
					$line->fk_multicurrency         = $objp->fk_multicurrency;
					$line->multicurrency_code       = $objp->multicurrency_code;
					$line->multicurrency_subprice   = $objp->multicurrency_subprice;
					$line->multicurrency_total_ht   = $objp->multicurrency_total_ht;
					$line->multicurrency_total_tva  = $objp->multicurrency_total_tva;
					$line->multicurrency_total_ttc  = $objp->multicurrency_total_ttc;

					//tabla adicional
					$line->fk_object = $objp->fk_object;
					$line->object = $objp->object;
					$line->fk_fabrication = $objp->fk_fabrication;
					$line->fk_fabricationdet = $objp->fk_fabricationdet;
					$line->fk_projet = $objp->fk_projet;
					$line->fk_projet_task = $objp->fk_projet_task;
					$line->fk_jobs = $objp->fk_jobs;
					$line->fk_jobsdet = $objp->fk_jobsdet;
					$line->fk_structure = $objp->fk_structure;
					$line->fk_poa = $objp->fk_poa;
					$line->partida = $objp->partida;
					$line->amount_ice = $objp->amount_ice;
					$line->discount = $objp->discount;


					$this->special_code        = $objp->special_code;
					$this->fk_parent_line      = $objp->fk_parent_line;

					$this->rang                = $objp->rang;

					$this->lines[$i]      = $line;

					$i++;
				}
				$this->db->free($result);

				return 1;
			}
			else
			{
				$this->error=$this->db->error()." sql=".$sql;
				return -1;
			}
		}
		else
		{
			$this->error=$this->db->error()." sql=".$sql;
			return -1;
		}
	}

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
		//$dirtpls=array_merge($conf->modules_parts['tpl'],array('purchase/tpl'));
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
	 *	Add order line
	 *
	 *	@param      string	$desc            		Description
	 *	@param      float	$pu_ht              	Unit price
	 *	@param      float	$qty             		Quantity
	 *	@param      float	$txtva           		Taux tva
	 *	@param      float	$txlocaltax1        	Localtax1 tax
	 *  @param      float	$txlocaltax2        	Localtax2 tax
	 *	@param      int		$fk_product      		Id product
	 *  @param      int		$fk_prod_fourn_price	Id supplier price
	 *  @param      string	$fourn_ref				Supplier reference
	 *	@param      float	$remise_percent  		Remise
	 *	@param      string	$price_base_type		HT or TTC
	 *	@param		float	$pu_ttc					Unit price TTC
	 *	@param		int		$type					Type of line (0=product, 1=service)
	 *	@param		int		$info_bits				More information
	 *  @param		bool	$notrigger				Disable triggers
	 *  @param		int		$date_start				Date start of service
	 *  @param		int		$date_end				Date end of service
	 *  @param		array	$array_options			extrafields array
	 *  @param 		string	$fk_unit 				Code of the unit to use. Null to use the default one
	 *	@return     int             				<=0 if KO, >0 if OK
	 */

	function addlineadd($desc, $pu_ht, $qty, $txtva, $txlocaltax1=0.0, $txlocaltax2=0.0, $fk_product=0, $fk_prod_fourn_price=0, $fourn_ref='', $remise_percent=0.0, $price_base_type='HT', $pu_ttc=0.0, $type=0, $info_bits=0, $notrigger=false, $date_start=null, $date_end=null, $array_options=0, $fk_unit=null,$lines)
	{
		global $langs,$mysoc,$conf;

		$error = 0;

		dol_syslog(get_class($this)."::addlineadd $desc, $pu_ht, $qty, $txtva, $txlocaltax1, $txlocaltax2, $fk_product, $fk_prod_fourn_price, $fourn_ref, $remise_percent, $price_base_type, $pu_ttc, $type, $fk_unit");
		include_once DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php';

		// Clean parameters
		if (! $qty) $qty=1;
		if (! $info_bits) $info_bits=0;
		if (empty($txtva)) $txtva=0;
		if (empty($txlocaltax1)) $txlocaltax1=0;
		if (empty($txlocaltax2)) $txlocaltax2=0;
		if (empty($remise_percent)) $remise_percent=0;

		$remise_percent=price2num($remise_percent);
		$qty=price2num($qty);
		$pu_ht=price2num($pu_ht);
		$pu_ttc=price2num($pu_ttc);
		$txtva = price2num($txtva);
		$txlocaltax1 = price2num($txlocaltax1);
		$txlocaltax2 = price2num($txlocaltax2);
		$fk_prod_fourn_price = $fk_prod_fourn_price+0;
		if ($price_base_type=='HT')
		{
			$pu=$pu_ht;
		}
		else
		{
			$pu=$pu_ttc;
		}
		$desc=trim($desc);
		// Check parameters
		if ($qty < 1 && ! $fk_product)
		{
			$this->error=$langs->trans("ErrorFieldRequired",$langs->trans("Product"));
			return -1333;
		}
		if ($type < 0) return -1444;

		if ($this->statut == 0)
		{
			$this->db->begin();

			if ($fk_product > 0)
			{
				if (empty($conf->global->SUPPLIER_ORDER_WITH_NOPRICEDEFINED))
				{
					// Check quantity is enough
					dol_syslog(get_class($this)."::addline we check supplier prices fk_product=".$fk_product." fk_prod_fourn_price=".$fk_prod_fourn_price." qty=".$qty." fourn_ref=".$fourn_ref);
					$prod = new Product($this->db, $fk_product);
					if ($prod->fetch($fk_product) > 0)
					{
						$ref = $prod->ref;
						//echo '<hr>envia '.$fk_prod_fourn_price.' qty '.$qty.' fkprod '.$fk_product.' fourref '.$fourn_ref.' '.$ref;
						$result=$prod->get_buyprice($fk_prod_fourn_price, $qty, $fk_product, $ref);
						// Search on couple $fk_prod_fourn_price/$qty first, then on triplet $qty/$fk_product/$fourn_ref
						if ($result > 0)
						{
							$label = $prod->libelle;
							$pu    = $prod->fourn_pu;
							$ref   = $prod->ref_fourn;
							$product_type = $prod->type;
						}
						if ($result == 0 || $result == -1)
						{
							$langs->load("errors");
							$this->error = "Ref " . $prod->ref . " " . $langs->trans("ErrorQtyTooLowForThisSupplier");
							$this->db->rollback();
							dol_syslog(get_class($this)."::addline result=".$result." - ".$this->error, LOG_DEBUG);
							return -19999;
						}
						if ($result < -1)
						{
							$this->error=$prod->error;
							$this->db->rollback();
							dol_syslog(get_class($this)."::addline result=".$result." - ".$this->error, LOG_ERR);
							return -1222;
						}
					}
					else
					{
						$this->error=$prod->error;
						return -10000;
					}
				}
				else
				{
					$product_type = $type;
					$prod = new Product($this->db, $fk_product);
					if ($prod->fetch($fk_product) > 0)
					{
						$ref = $prod->ref;
						$label = $prod->label;
					}
				}
			}
			else
			{
				$product_type = $type;
				$label = $lines->label;
			}

			// Calcul du total TTC et de la TVA pour la ligne a partir de
			// qty, pu, remise_percent et txtva
			// TRES IMPORTANT: C'est au moment de l'insertion ligne qu'on doit stocker
			// la part ht, tva et ttc, et ce au niveau de la ligne qui a son propre taux tva.

			$localtaxes_type=getLocalTaxesFromRate($txtva,0,$mysoc,$this->thirdparty);
			$txtva = preg_replace('/\s*\(.*\)/','',$txtva);  // Remove code into vatrate.

			//$tabprice = calcul_price_total($qty, $pu, $remise_percent, $txtva, $txlocaltax1, $txlocaltax2, 0, $price_base_type, $info_bits, $product_type, $this->thirdparty, $localtaxes_type, 100, $this->multicurrency_tx);
			$total_ht  = $tabprice[0];
			$total_tva = $tabprice[1];
			$total_ttc = $tabprice[2];
			$total_localtax1 = $tabprice[9];
			$total_localtax2 = $tabprice[10];

			$total_ht  = $lines->total_ht+0;
			$total_tva = $lines->total_tva+0;
			$total_ttc = $lines->total_ttc+0;
			$total_localtax1 = $lines->total_localtax1+0;
			$total_localtax2 = $lines->total_localtax2+0;

			// MultiCurrency
			$multicurrency_total_ht  = $tabprice[16]+0;
			$multicurrency_total_tva = $tabprice[17]+0;
			$multicurrency_total_ttc = $tabprice[18]+0;

			$localtax1_type=$lines->localtax1_type;
			$localtax2_type=$lines->localtax2_type;
			$remise = $lines->remise+0;
			$subprice = price2num($pu_ht,'MU');

			// TODO We should use here $this->line=new CommandeFournisseurLigne($this->db); and $this->line->insert(); to work loke other object (proposal, order, invoice)
			$sql = "INSERT INTO ".MAIN_DB_PREFIX."commande_fournisseurdet";
			$sql.= " (fk_commande, label, description, date_start, date_end,";
			$sql.= " fk_product, product_type,";
			$sql.= " qty, tva_tx, localtax1_tx, localtax2_tx, localtax1_type, localtax2_type, remise_percent, subprice, ref,price,remise,";
			$sql.= " total_ht, total_tva, total_localtax1, total_localtax2, total_ttc, fk_unit,";
			$sql.= " fk_multicurrency, multicurrency_code, multicurrency_subprice, multicurrency_total_ht, multicurrency_total_tva, multicurrency_total_ttc";
			$sql.= ")";
			$sql.= " VALUES (".$this->id.", '" . $this->db->escape($label) . "','" . $this->db->escape($desc) . "',";
			$sql.= " ".($date_start?"'".$this->db->idate($date_start)."'":"null").",";
			$sql.= " ".($date_end?"'".$this->db->idate($date_end)."'":"null").",";
			if ($fk_product>0) { $sql.= $fk_product.","; }
			else { $sql.= "null,"; }
			$sql.= "'".$product_type."',";
			$sql.= "'".$qty."', ".$txtva.", ".$txlocaltax1.", ".$txlocaltax2;

			$sql.= ", '".$localtax1_type."',";
			$sql.= " '".$localtax2_type."'";

			$sql.= ", ".$remise_percent.",'".price2num($subprice,'MU')."','".$ref."',";
			$sql.= "'".price2num($pu_ttc)."',";
			$sql.= "'".price2num($remise)."',";
			$sql.= "'".price2num($total_ht)."',";
			$sql.= "'".price2num($total_tva)."',";
			$sql.= "'".price2num($total_localtax1)."',";
			$sql.= "'".price2num($total_localtax2)."',";
			$sql.= "'".price2num($total_ttc)."',";
			$sql.= ($fk_unit ? "'".$this->db->escape($fk_unit)."'":"null");
			$sql.= ", ".($this->fk_multicurrency ? $this->fk_multicurrency : "null");
			$sql.= ", '".$this->db->escape($this->multicurrency_code)."'";
			$sql.= ", ".price2num($pu_ht * $this->multicurrency_tx);
			$sql.= ", ".$multicurrency_total_ht;
			$sql.= ", ".$multicurrency_total_tva;
			$sql.= ", ".$multicurrency_total_ttc;
			$sql.= ")";

			$resql=$this->db->query($sql);
			//print '<hr>'.$sql;exit;
			if ($resql)
			{
				$idligne = $this->db->last_insert_id(MAIN_DB_PREFIX.'commande_fournisseurdet');

				if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED))
				// For avoid conflicts if trigger used
				{
					$linetmp = new CommandeFournisseurLigne($this->db);
					//$linetmp->id=$this->db->last_insert_id(MAIN_DB_PREFIX.'commande_fournisseurdet');
					$linetmp->id = $idligne;
					$linetmp->array_options = $array_options;
					$result=$linetmp->insertExtraFields();
					if ($result < 0)
					{
						$error++;
					}
				}

				if (! $error && ! $notrigger)
				{
					global $conf, $langs, $user;
					// Call trigger
					$result=$this->call_trigger('LINEORDER_SUPPLIER_CREATE',$user);
					if ($result < 0)
					{
						$this->db->rollback();
						return -1;
					}
					// End call triggers
				}

				//$this->update_price('','auto');

				$this->db->commit();
				return $idligne;
			}
			else
			{
				$this->error=$this->db->error();
				$this->db->rollback();
				return -1;
			}
		}
	}


	/**
	 *  Update line
	 *
	 *  @param      int         $rowid              Id de la ligne de facture
	 *  @param      string      $desc               Description de la ligne
	 *  @param      double      $pu                 Prix unitaire
	 *  @param      double      $qty                Quantity
	 *  @param      double      $remise_percent     Pourcentage de remise de la ligne
	 *  @param      double      $txtva              Taux TVA
	 *  @param      double      $txlocaltax1        Localtax1 tax
	 *  @param      double      $txlocaltax2        Localtax2 tax
	 *  @param      double      $price_base_type    Type of price base
	 *  @param      int         $info_bits          Miscellaneous informations
	 *  @param      int         $type               Type of line (0=product, 1=service)
	 *  @param      int         $notrigger          Disable triggers
	 *  @param      timestamp   $date_start         Date start of service
	 *  @param      timestamp   $date_end           Date end of service
	 *  @param      array       $array_options      Extrafields array
	 *  @param      string      $fk_unit            Code of the unit to use. Null to use the default one
	 *  @return     int                             < 0 if error, > 0 if ok
	 */
	function updatelineadd($rowid,$desc, $pu_ht, $qty, $txtva, $txlocaltax1=0.0, $txlocaltax2=0.0, $fk_product=0, $fk_prod_fourn_price=0, $fourn_ref='', $remise_percent=0.0, $price_base_type='HT', $pu_ttc=0.0, $type=0, $info_bits=0, $notrigger=false, $date_start=null, $date_end=null, $array_options=0, $fk_unit=null,$lines)

	//function updatelineadd($rowid, $desc, $pu, $qty, $remise_percent, $txtva, $txlocaltax1=0, $txlocaltax2=0, $price_base_type='HT', $info_bits=0, $type=0, $notrigger=false, $date_start='', $date_end='', $array_options=0, $fk_unit=null)
	{
		global $mysoc;
		dol_syslog(get_class($this)."::updatelineadd $rowid, $desc, $pu, $qty, $remise_percent, $txtva, $price_base_type, $info_bits, $type, $fk_unit");
		include_once DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php';

		if ($this->brouillon)
		{
			$this->db->begin();

			// Clean parameters
			if (empty($qty)) $qty=0;
			if (empty($info_bits)) $info_bits=0;
			if (empty($txtva)) $txtva=0;
			if (empty($txlocaltax1)) $txlocaltax1=0;
			if (empty($txlocaltax2)) $txlocaltax2=0;
			if (empty($remise)) $lines->remise=0;
			if (empty($remise_percent)) $remise_percent=0;

			$remise_percent=price2num($remise_percent);
			$qty=price2num($qty);
			if (! $qty) $qty=1;
			$pu_ht = price2num($pu_ht,'MU');
			$pu_ttc = price2num($pu_ttc,'MU');
			$txtva=price2num($txtva);
			$txlocaltax1=price2num($lines->txlocaltax1,'MU');
			$txlocaltax2=price2num($lines->txlocaltax2,'MU');

			// Check parameters
			if ($type < 0) return -1;

			// Calcul du total TTC et de la TVA pour la ligne a partir de
			// qty, pu, remise_percent et txtva
			// TRES IMPORTANT: C'est au moment de l'insertion ligne qu'on doit stocker
			// la part ht, tva et ttc, et ce au niveau de la ligne qui a son propre taux tva.

			$localtaxes_type=getLocalTaxesFromRate($txtva,0,$mysoc, $this->thirdparty);
			$txtva = preg_replace('/\s*\(.*\)/','',$txtva);  // Remove code into vatrate.

			//$tabprice=calcul_price_total($qty, $pu, $remise_percent, $txtva, $txlocaltax1, $txlocaltax2, 0, $price_base_type, $info_bits, $type, $this->thirdparty, $localtaxes_type, 100, $this->multicurrency_tx);
			$total_ht  = $tabprice[0];
			$total_tva = $tabprice[1];
			$total_ttc = $tabprice[2];
			$total_localtax1 = $tabprice[9];
			$total_localtax2 = $tabprice[10];

			// MultiCurrency
			$multicurrency_total_ht  = $tabprice[16];
			$multicurrency_total_tva = $tabprice[17];
			$multicurrency_total_ttc = $tabprice[18];

			$localtax1_type=$localtaxes_type[0];
			$localtax2_type=$localtaxes_type[2];


			$total_ht  = price2num($lines->total_ht+0,'MU');
			$total_tva = price2num($lines->total_tva+0,'MU');
			$total_ttc = price2num($lines->total_ttc+0,'MU');
			$total_localtax1 = price2num($lines->total_localtax1+0,'MU');
			$total_localtax2 = price2num($lines->total_localtax2+0,'MU');

			// MultiCurrency
			$multicurrency_total_ht  = $tabprice[16]+0;
			$multicurrency_total_tva = $tabprice[17]+0;
			$multicurrency_total_ttc = $tabprice[18]+0;

			$txlocaltax1=$lines->localtax1_tx;
			$txlocaltax2=$lines->localtax2_tx;
			$localtax1_type=$lines->localtax1_type;
			$localtax2_type=$lines->localtax2_type;
			$ref = $lines->ref;
			$label = $lines->label;
			$remise = $lines->remise+0;
			$subprice = price2num($pu_ht,'MU');
			$price = price2num($pu_ttc,'MU');

			// Mise a jour ligne en base
			$sql = "UPDATE ".MAIN_DB_PREFIX."commande_fournisseurdet SET";
			$sql.= " ref='".$this->db->escape($ref)."'";
			$sql.= ",label='".$this->db->escape($label)."'";
			$sql.= ",description='".$this->db->escape($desc)."'";
			$sql.= ",subprice='".price2num($subprice)."'";
			$sql.= ",remise='".price2num($remise)."'";
			$sql.= ",price='".price2num($price)."'";
			$sql.= ",remise_percent='".price2num($remise_percent)."'";
			$sql.= ",tva_tx='".price2num($txtva)."'";
			$sql.= ",localtax1_tx='".price2num($txlocaltax1)."'";
			$sql.= ",localtax2_tx='".price2num($txlocaltax2)."'";
			$sql.= ",localtax1_type='".$localtax1_type."'";
			$sql.= ",localtax2_type='".$localtax2_type."'";
			$sql.= ",qty='".price2num($qty)."'";
			$sql.= ",date_start=".(! empty($date_start)?"'".$this->db->idate($date_start)."'":"null");
			$sql.= ",date_end=".(! empty($date_end)?"'".$this->db->idate($date_end)."'":"null");
			$sql.= ",info_bits='".$info_bits."'";
			$sql.= ",total_ht='".price2num($total_ht)."'";
			$sql.= ",total_tva='".price2num($total_tva)."'";
			$sql.= ",total_localtax1='".price2num($total_localtax1)."'";
			$sql.= ",total_localtax2='".price2num($total_localtax2)."'";
			$sql.= ",total_ttc='".price2num($total_ttc)."'";
			$sql.= ",product_type=".$type;
			$sql.= ($fk_unit ? ",fk_unit='".$this->db->escape($fk_unit)."'":", fk_unit=null");

			// Multicurrency
			$sql.= " , multicurrency_subprice=".price2num($subprice * $this->multicurrency_tx)."";
			$sql.= " , multicurrency_total_ht=".price2num($multicurrency_total_ht)."";
			$sql.= " , multicurrency_total_tva=".price2num($multicurrency_total_tva)."";
			$sql.= " , multicurrency_total_ttc=".price2num($multicurrency_total_ttc)."";
			$sql.= " , price=".price2num($pu_ttc)."";

			$sql.= " WHERE rowid = ".$rowid;

			dol_syslog(get_class($this)."::updateline", LOG_DEBUG);
			$result = $this->db->query($sql);
			if ($result > 0)
			{
				$this->rowid = $rowid;
				if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED)) // For avoid conflicts if trigger used
				{
					$tmpline = new CommandeFournisseurLigne($this->db);
					$tmpline->id=$this->rowid;
					$tmpline->array_options = $array_options;
					$result=$tmpline->insertExtraFields();
					if ($result < 0)
					{
						$error++;
					}
				}

				if (! $error && ! $notrigger)
				{
					global $conf, $langs, $user;
					// Call trigger
					$result=$this->call_trigger('LINEORDER_SUPPLIER_UPDATE',$user);
					if ($result < 0)
					{
						$this->db->rollback();
						return -1;
					}
					// End call triggers
				}

				// Mise a jour info denormalisees au niveau facture
				if (! $error)
				{
					//$this->update_price('','auto');
				}

				if (! $error)
				{
					$this->db->commit();
					return $result;
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
				$this->db->rollback();
				return -1;
			}
		}
		else
		{
			$this->error="Order status makes operation forbidden";
			dol_syslog(get_class($this)."::updatelineadd ".$this->error, LOG_ERR);
			return -2;
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

		if ($this->element == 'supplier_proposal')
		{
			print '<td class="linerefsupplier" align="right"><span id="title_fourn_ref">'.$langs->trans("SupplierProposalRefFourn").'</span></td>';
		}

		// VAT
		print '<td class="linecolvat" align="right" width="50">'.$langs->trans('VAT').'</td>';

		// Price HT
		//print '<td class="linecoluht" align="right" width="80">'.$langs->trans('PriceUHT').'</td>';

		// Multicurrency
		if (!empty($conf->multicurrency->enabled)) print '<td class="linecoluht_currency" align="right" width="80">'.$langs->trans('PriceUHTCurrency').'</td>';

		//if ($inputalsopricewithtax)
		print '<td align="right" width="80">'.$langs->trans('PriceUTTC').'</td>';

		// Qty
		print '<td class="linecolqty" align="right">'.$langs->trans('Qty').'</td>';

		if($conf->global->PRODUCT_USE_UNITS)
		{
			print '<td class="linecoluseunit" align="left">'.$langs->trans('Unit').'</td>';
		}

		// Reduction short
		print '<td class="linecoldiscount" align="right">'.$langs->trans('ReductionShortpercent').' % '.$langs->trans('ReductionShortpercentdos').'</td>';
		if ($this->element != 'order_supplier')
			print '<td class="linecoldiscount" align="right">'.$langs->trans('ICE').'</td>';

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
		$objtmp = new CommandeFournisseurLigne($this->db);
		foreach ($this->lines as $line)
		{
			$objtmp->fetch($line->id);
			$line->discount = $objtmp->remise;
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

		//agregamos la tabla adicional a line
		require_once DOL_DOCUMENT_ROOT.'/purchase/class/commandefournisseurdetadd.class.php';
		$objtmp = new Commandefournisseurdetadd($this->db);
		$restmp = $objtmp->fetch(0,$line->id);
		if ($restmp == 1)
		{
			$line->fk_projet = $objtmp->fk_projet;
			$line->fk_projet_task = $objtmp->fk_projet_task;
			$line->fk_fabrication = $objtmp->fk_fabrication;
			$line->fk_fabricationdet = $objtmp->fk_fabricationdet;
			$line->fk_jobs = $objtmp->fk_jobs;
			$line->fk_jobsdet = $objtmp->fk_jobsdet;
			$line->fk_structure = $objtmp->fk_structure;
			$line->fk_poa = $objtmp->fk_poa;
			$line->partida = $objtmp->partida;
		}
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

			//$line->pu_ttc = price2num($line->subprice * (1 + ($line->tva_tx/100)), 'MU');
			//$line->pu_ttc = $line->price;
			// Output template part (modules that overwrite templates must declare this into descriptor)
			// Use global variables + $dateSelector + $seller and $buyer
			//$dirtpls=array_merge($conf->modules_parts['tpl'],array('/purchase/tpl'));
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
			//$line->pu_ttc = price2num($line->subprice * (1 + ($line->tva_tx/100)), 'MU');

			// Output template part (modules that overwrite templates must declare this into descriptor)
			// Use global variables + $dateSelector + $seller and $buyer
			//$dirtpls=array_merge($conf->modules_parts['tpl'],array('/purchase/tpl'));
			$dirtpls=array_merge($conf->modules_parts['tpl'],array('/core/tpl2'));
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
	 *  Get object and lines from database
	 *
	 *  @param  int     $id         Id of order to load
	 *  @param  string  $ref        Ref of object
	 *  @return int                 >0 if OK, <0 if KO, 0 if not found
	 */
	function fetchOrderlines($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	{
		global $conf;

		// Check parameters
		$sql = "SELECT c.rowid, c.ref, ref_supplier, c.fk_soc, c.fk_statut, c.amount_ht, c.total_ht, c.total_ttc, c.tva,";
		$sql.= " c.localtax1, c.localtax2, ";
		$sql.= " c.date_creation, c.date_valid, c.date_approve, c.date_approve2,";
		$sql.= " c.fk_user_author, c.fk_user_valid, c.fk_user_approve, c.fk_user_approve2,";
		$sql.= " c.date_commande as date_commande, c.date_livraison as date_livraison, c.fk_cond_reglement, c.fk_mode_reglement, c.fk_projet as fk_project, c.remise_percent, c.source, c.fk_input_method,";
		$sql.= " c.fk_account,";
		$sql.= " c.note_private, c.note_public, c.model_pdf, c.extraparams, c.billed,";
		$sql.= " c.fk_multicurrency, c.multicurrency_code, c.multicurrency_tx, c.multicurrency_total_ht, c.multicurrency_total_tva, c.multicurrency_total_ttc,";
		$sql.= " cm.libelle as methode_commande,";
		$sql.= " cr.code as cond_reglement_code, cr.libelle as cond_reglement_libelle,";
		$sql.= " p.code as mode_reglement_code, p.libelle as mode_reglement_libelle";
		$sql.= ', c.fk_incoterms, c.location_incoterms';
		$sql.= ', i.libelle as libelle_incoterms';

		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as c';
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_payment_term as cr ON (c.fk_cond_reglement = cr.rowid)";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_paiement as p ON (c.fk_mode_reglement = p.id)";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_input_method as cm ON cm.rowid = c.fk_input_method";
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_incoterms as i ON c.fk_incoterms = i.rowid';

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

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield,$sortorder);
		}
		if (!empty($limit)) {
			$sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();

		dol_syslog(get_class($this)."::fetchAll", LOG_DEBUG);
		$resql = $this->db->query($sql);
		$this->linesdet = array();
		if ($resql)
		{
			$numj = $this->db->num_rows($resql);
			if ($numj)
			{
				$j =0;
				while ($j < $numj)
				{
					$obj = $this->db->fetch_object($resql);
					if (! $obj)
					{
						$this->error='Bill with id '.$id.' not found';
						dol_syslog(get_class($this).'::fetch '.$this->error);
						return 0;
					}



					$sql = "SELECT l.rowid, l.ref as ref_supplier, l.fk_product, l.product_type, l.label, l.description,";
					$sql.= " l.qty, l.fk_commande, ";
					$sql.= " l.tva_tx, l.remise_percent, l.subprice,";
					$sql.= " l.localtax1_tx, l. localtax2_tx, l.total_localtax1, l.total_localtax2,";
					$sql.= " l.total_ht, l.total_tva, l.total_ttc, l.special_code, l.fk_parent_line, l.rang,";
					$sql.= " p.rowid as product_id, p.ref as product_ref, p.label as product_label, p.description as product_desc,";
					$sql.= " l.fk_unit,";
					$sql.= " l.date_start, l.date_end,";
					$sql.= ' l.fk_multicurrency, l.multicurrency_code, l.multicurrency_subprice, l.multicurrency_total_ht, l.multicurrency_total_tva, l.multicurrency_total_ttc';
					$sql.= " FROM ".MAIN_DB_PREFIX."commande_fournisseurdet as l";
					$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON l.fk_product = p.rowid';
					$sql.= " WHERE l.fk_commande = ".$obj->rowid;
					$sql.= " ORDER BY l.rang, l.rowid";


					dol_syslog(get_class($this)."::fetchOrderlines get lines", LOG_DEBUG);
					$result = $this->db->query($sql);
					if ($result)
					{
						$num = $this->db->num_rows($result);
						$i = 0;

						while ($i < $num)
						{
							$objp                  = $this->db->fetch_object($result);

							$line                 = new CommandeFournisseurLigne($this->db);

							$line->id                  = $objp->rowid;
							$line->fk_commande         = $obj->rowid;
							$line->reforder			   = $obj->ref;
							$line->desc                = $objp->description;
							$line->description         = $objp->description;
							$line->qty                 = $objp->qty;
							$line->tva_tx              = $objp->tva_tx;
							$line->localtax1_tx        = $objp->localtax1_tx;
							$line->localtax2_tx        = $objp->localtax2_tx;
							$line->subprice            = $objp->subprice;
							$line->pu_ht               = $objp->subprice;
							$line->remise_percent      = $objp->remise_percent;
							$line->total_ht            = $objp->total_ht;
							$line->total_tva           = $objp->total_tva;
							$line->total_localtax1     = $objp->total_localtax1;
							$line->total_localtax2     = $objp->total_localtax2;
							$line->total_ttc           = $objp->total_ttc;
							$line->product_type        = $objp->product_type;

							$line->fk_product          = $objp->fk_product;

							$line->libelle             = $objp->product_label;
							$line->product_label       = $objp->product_label;
							$line->product_desc        = $objp->product_desc;

							$line->ref                 = $objp->product_ref;
							$line->product_ref         = $objp->product_ref;
							$line->ref_fourn           = $objp->ref_supplier;
							$line->ref_supplier        = $objp->ref_supplier;

							$line->date_start          = $this->db->jdate($objp->date_start);
							$line->date_end            = $this->db->jdate($objp->date_end);
							$line->fk_unit             = $objp->fk_unit;

							// Multicurrency
							$line->fk_multicurrency         = $objp->fk_multicurrency;
							$line->multicurrency_code       = $objp->multicurrency_code;
							$line->multicurrency_subprice   = $objp->multicurrency_subprice;
							$line->multicurrency_total_ht   = $objp->multicurrency_total_ht;
							$line->multicurrency_total_tva  = $objp->multicurrency_total_tva;
							$line->multicurrency_total_ttc  = $objp->multicurrency_total_ttc;

							$this->special_code        = $objp->special_code;
							$this->fk_parent_line      = $objp->fk_parent_line;

							$this->rang                = $objp->rang;

							$this->linesdet[]      = $line;

							$i++;
						}
					}
					else
					{
						$this->error=$this->db->error()." sql=".$sql;
						return -1;
					}
					$j++;
				}
				$this->db->free($result);

				return 1;
			}
			return 0;
		}
		else
		{
			$this->error=$this->db->error()." sql=".$sql;
			return -1;
		}
	}

	/**
	 *  Get object and lines from database
	 *
	 *  @param  int     $id         Id of order to load
	 *  @param  string  $ref        Ref of object
	 *  @return int                 >0 if OK, <0 if KO, 0 if not found
	 */
	function fetchOrder($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	{
		global $conf;

		// Check parameters
		$sql = "SELECT c.rowid, c.ref, ref_supplier, c.fk_soc, c.fk_statut, c.amount_ht, c.total_ht, c.total_ttc, c.tva,";
		$sql.= " c.localtax1, c.localtax2, ";
		$sql.= " c.date_creation, c.date_valid, c.date_approve, c.date_approve2,";
		$sql.= " c.fk_user_author, c.fk_user_valid, c.fk_user_approve, c.fk_user_approve2,";
		$sql.= " c.date_commande as date_commande, c.date_livraison as date_livraison, c.fk_cond_reglement, c.fk_mode_reglement, c.fk_projet as fk_project, c.remise_percent, c.source, c.fk_input_method,";
		$sql.= " c.fk_account,";
		$sql.= " c.note_private, c.note_public, c.model_pdf, c.extraparams, c.billed,";
		$sql.= " c.fk_multicurrency, c.multicurrency_code, c.multicurrency_tx, c.multicurrency_total_ht, c.multicurrency_total_tva, c.multicurrency_total_ttc,";
		$sql.= " cm.libelle as methode_commande,";
		$sql.= " cr.code as cond_reglement_code, cr.libelle as cond_reglement_libelle,";
		$sql.= " p.code as mode_reglement_code, p.libelle as mode_reglement_libelle";
		$sql.= ', c.fk_incoterms, c.location_incoterms';
		$sql.= ', i.libelle as libelle_incoterms';

		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as c';
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_payment_term as cr ON (c.fk_cond_reglement = cr.rowid)";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_paiement as p ON (c.fk_mode_reglement = p.id)";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_input_method as cm ON cm.rowid = c.fk_input_method";
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_incoterms as i ON c.fk_incoterms = i.rowid';

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

		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield,$sortorder);
		}
		if (!empty($limit)) {
			$sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();

		dol_syslog(get_class($this)."::fetchAll", LOG_DEBUG);
		$resql = $this->db->query($sql);
		$this->linesdet = array();
		if ($resql)
		{
			$numj = $this->db->num_rows($resql);
			if ($numj)
			{
				$j =0;
				while ($j < $numj)
				{
					$obj = $this->db->fetch_object($resql);
					if (! $obj)
					{
						$this->error='Bill with id '.$id.' not found';
						dol_syslog(get_class($this).'::fetch '.$this->error);
						return 0;
					}
					$line                 = new FournisseurCommandeext($this->db);
					$line->id                  = $obj->rowid;
					$line->ref 				   = $obj->ref;
					$line->ref_supplier        = $obj->ref_supplier;
					$line->fk_soc 	 	       = $obj->fk_soc;
					$line->fk_statut           = $obj->fk_statut;
					$line->amount_ht           = $obj->amount_ht;
					$line->localtax1    	   = $obj->localtax1;
					$line->localtax2 	       = $obj->localtax2;
					$line->date_commande       = $this->db->jdate($obj->date_commande);
					$line->fk_projet           = $obj->fk_project;
					$line->remise_percent      = $obj->remise_percent;
					$line->total_ht            = $obj->total_ht;
					$line->total_tva           = $obj->total_tva;
					$line->total_localtax1     = $obj->total_localtax1;
					$line->total_localtax2     = $obj->total_localtax2;
					$line->total_ttc           = $obj->total_ttc;
							// Multicurrency
					$line->fk_multicurrency         = $obj->fk_multicurrency;
					$line->multicurrency_code       = $obj->multicurrency_code;
					$line->multicurrency_tx 		= $obj->multicurrency_tx;
					$line->multicurrency_total_ht   = $obj->multicurrency_total_ht;
					$line->multicurrency_total_tva  = $obj->multicurrency_total_tva;
					$line->multicurrency_total_ttc  = $obj->multicurrency_total_ttc;
					$this->lines[] = $line;
					$j++;
				}
				$this->db->free($result);
			}
			return $numj;
		}
		else
		{
			$this->error=$this->db->error()." sql=".$sql;
			return -1;
		}
	}


	function commande_getsum()
	{
		global $conf,$langs;
		$product_commande = array();
		$sql = "SELECT l.rowid, l.fk_unit, l.fk_product, l.subprice, l.remise_percent, SUM(l.qty) as qty,";
		$sql.= " p.ref, p.label, p.tobatch";
		$sql.= " FROM ".MAIN_DB_PREFIX."commande_fournisseurdet as l";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product as p ON l.fk_product=p.rowid";
		$sql.= " WHERE l.fk_commande = ".$this->id;
		if(empty($conf->global->STOCK_SUPPORTS_SERVICES)) $sql.= " AND l.product_type = 0";
		$sql.= " GROUP BY p.ref, p.label, p.tobatch, l.rowid, l.fk_unit,l.fk_product, l.subprice, l.remise_percent";
			// Calculation of amount dispatched is done per fk_product so we must group by fk_product
		$sql.= " ORDER BY p.ref, p.label";

		$resql = $this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			$i = 0;

			$nbfreeproduct=0;
			$nbproduct=0;
			$var=false;
			while ($i < $num)
			{
				$objp = $this->db->fetch_object($resql);

					// On n'affiche pas les produits personnalises
				if (! $objp->fk_product > 0)
				{
					$nbfreeproduct++;
				}
				else
				{
					$product_commande[$objp->rowid] = $objp->qty;
				}
				$i++;
			}
			$this->db->free($resql);
		}
		return $product_commande;
	}

	function commande_dispatch()
	{
		global $conf,$langs;
		require_once DOL_DOCUMENT_ROOT.'/purchase/class/unitconv.class.php';
		require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
		$prodtmp  = new Product($this->db);
		$unitconv = new Unitconv($this->db);

		$products_dispatched = array();
		$sql = "SELECT l.rowid, cfd.fk_product, l.fk_unit, sum(cfd.qty) as qty";
		$sql.= " FROM ".MAIN_DB_PREFIX."commande_fournisseur_dispatch as cfd";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."commande_fournisseurdet as l on l.rowid = cfd.fk_commandefourndet";
		$sql.= " WHERE cfd.fk_commande = ".$this->id;
		$sql.= " GROUP BY l.rowid, cfd.fk_product, l.fk_unit ";

		$resql = $this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			$i = 0;

			if ($num)
			{
				while ($i < $num)
				{
					$objd = $this->db->fetch_object($resql);

					$prodtmp->fetch($objd->fk_product);
					$lConvert = false;
					if ($prodtmp->fk_unit != $objd->fk_unit)
						$lConvert = true;
					if ($lConvert)
					{
							//buscamos la conversion
						$filter = array(1=>1);
						$filterstatic = " AND t.fk_product = ".$objd->fk_product;
						$unitconv->fetchAll('','',0,0,$filter,'AND',$filterstatic,false);
						foreach ((array) $unitconv->lines AS $j => $lin)
						{
							if ($lin->fk_unit_ext == $objd->fk_unit)
							{
								if ($lin->type_fc == 'M')
								{
									$objd->qty = $objd->qty / $lin->fc;
								}
								if ($lin->type_fc == 'D')
								{
									$objd->qty = $objd->qty * $lin->fc;
								}
							}
						}
					}

					$products_dispatched[$objd->rowid] = price2num($objd->qty, 5);
					$i++;
				}
			}
			$this->db->free($resql);
		}
		return $products_dispatched;
	}

	/**
	 *  Load line order
	 *
	 *  @param  int		$rowid      Id line order
	 *	@return	int					<0 if KO, >0 if OK
	 */
	public function fetch_linesadd()
	{
		$sql = 'SELECT cd.rowid, cd.fk_commande, cd.fk_product, cd.product_type, cd.description, cd.qty, cd.tva_tx,';
		$sql.= ' cd.localtax1_tx, cd.localtax2_tx, cd.ref,';
		$sql.= ' cd.remise, cd.remise_percent, cd.subprice, cd.price,';
		$sql.= ' cd.info_bits, cd.total_ht, cd.total_tva, cd.total_ttc,';
		$sql.= ' cd.total_localtax1, cd.total_localtax2,';
		$sql.= ' la.fk_object, la.object, la.fk_fabrication, la.fk_fabricationdet, la.fk_projet, la.fk_projet_task, la.fk_jobs, ';
		$sql.= ' la.fk_jobsdet, la.fk_structure, la.fk_poa, la.partida, la.amount_ice, la.discount, ';

		$sql.= ' p.ref as product_ref, p.label as product_libelle, p.description as product_desc,';
		$sql.= ' cd.date_start, cd.date_end, cd.fk_unit,';
		$sql.= ' cd.multicurrency_subprice, cd.multicurrency_total_ht, cd.multicurrency_total_tva, cd.multicurrency_total_ttc';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'commande_fournisseurdet as cd';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'commande_fournisseurdet_add as la ON la.fk_commande_fournisseurdet = cd.rowid';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON cd.fk_product = p.rowid';
		$sql.= ' WHERE cd.fk_commande = '.$this->id;

		$result = $this->db->query($sql);
		$this->lines = array();
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;
			while ($i < $num)
			{
				$objp = $this->db->fetch_object($result);

				$line = new CommandeFournisseurLigneext($this->db);

				$line->rowid            = $objp->rowid;
				$line->id               = $objp->rowid;
				$line->fk_commande      = $objp->fk_commande;
				$line->desc             = $objp->description;
				$line->qty              = $objp->qty;
				$line->ref_fourn        = $objp->ref;
				$line->ref_supplier     = $objp->ref;
				$line->subprice         = $objp->subprice;
				$line->price         = $objp->price;
				$line->tva_tx           = $objp->tva_tx;
				$line->localtax1_tx		= $objp->localtax1_tx;
				$line->localtax2_tx		= $objp->localtax2_tx;
				$line->remise           = $objp->remise;
				$line->remise_percent   = $objp->remise_percent;
				$line->fk_product       = $objp->fk_product;
				$line->info_bits        = $objp->info_bits;
				$line->total_ht         = $objp->total_ht;
				$line->total_tva        = $objp->total_tva;
				$line->total_localtax1	= $objp->total_localtax1;
				$line->total_localtax2	= $objp->total_localtax2;
				$line->total_ttc        = $objp->total_ttc;
				$line->product_type     = $objp->product_type;

				$line->ref	            = $objp->product_ref;
				$line->product_ref      = $objp->product_ref;
				$line->product_libelle  = $objp->product_libelle;
				$line->product_desc     = $objp->product_desc;

				$line->date_start       		= $this->db->jdate($objp->date_start);
				$line->date_end         		= $this->db->jdate($objp->date_end);
				$line->fk_unit          		= $objp->fk_unit;

				$line->multicurrency_subprice	= $objp->multicurrency_subprice;
				$line->multicurrency_total_ht	= $objp->multicurrency_total_ht;
				$line->multicurrency_total_tva	= $objp->multicurrency_total_tva;
				$line->multicurrency_total_ttc	= $objp->multicurrency_total_ttc;
			//comamndefournisseurdetadd
				$line->fk_object 		= $objp->fk_object;
				$line->object 			= $objp->object;
				$line->fk_fabrication 		= $objp->fk_fabrication;
				$line->fk_fabricationdet 	= $objp->fk_fabricationdet;
				$line->fk_projet 		= $objp->fk_projet;
				$line->fk_projet_task 		= $objp->fk_projet_task;
				$line->fk_jobs 			= $objp->fk_jobs;
				$line->fk_jobsdet 		= $objp->fk_jobsdet;
				$line->fk_structure		= $objp->fk_structure;
				$line->fk_poa			= $objp->fk_poa;
				$line->partida 			= $objp->partida;
				$line->amount_ice 		= $objp->amount_ice;
				$line->discount 		= $objp->discount;
				$this->lines[$i] = $line;
				$i++;
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

}

/**
 *  Class to manage line orders
 */
class CommandeFournisseurLigneext extends CommandeFournisseurLigne
{

	var $aData;
	function get_sum_taxes($id)
	{
		if (empty($id)) return -1;

		$sql = 'SELECT l.rowid, l.fk_product, l.fk_parent_line, l.product_type, l.fk_commande, l.label as custom_label, l.description, l.price, l.qty, l.tva_tx,';
		$sql.= ' l.localtax1_tx, l.localtax2_tx, l.remise_percent, l.subprice,  l.rang, l.info_bits, l.special_code,';
		$sql.= ' l.total_ht, l.total_ttc, l.total_tva, l.total_localtax1, l.total_localtax2, ';
		$sql.= ' l.fk_unit,';
		$sql.= ' l.fk_multicurrency, l.multicurrency_code, l.multicurrency_subprice, l.multicurrency_total_ht, l.multicurrency_total_tva, l.multicurrency_total_ttc';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'commande_fournisseurdet as l';
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
	 *  Load line order
	 *
	 *  @param  int		$rowid      Id line order
	 *	@return	int					<0 if KO, >0 if OK
	 */
	function fetchline($rowid)
	{
		$sql = 'SELECT cd.rowid, cd.fk_commande, cd.fk_product, cd.product_type, cd.description, cd.qty, cd.tva_tx,';
		$sql.= ' cd.localtax1_tx, cd.localtax2_tx,';
		$sql.= ' cd.remise, cd.remise_percent, cd.subprice, cd.price,';
		$sql.= ' cd.info_bits, cd.total_ht, cd.total_tva, cd.total_ttc,';
		$sql.= ' cd.total_localtax1, cd.total_localtax2,';
		$sql.= ' p.ref as product_ref, p.label as product_libelle, p.description as product_desc,';
		$sql.= ' cd.date_start, cd.date_end, cd.fk_unit';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'commande_fournisseurdet as cd';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON cd.fk_product = p.rowid';
		$sql.= ' WHERE cd.rowid = '.$rowid;
		$result = $this->db->query($sql);
		if ($result)
		{
			$objp = $this->db->fetch_object($result);
			$this->rowid            = $objp->rowid;
			$this->fk_commande      = $objp->fk_commande;
			$this->desc             = $objp->description;
			$this->qty              = $objp->qty;
			$this->price 			= $objp->price;
			$this->subprice         = $objp->subprice;
			$this->tva_tx           = $objp->tva_tx;
			$this->localtax1_tx		= $objp->localtax1_tx;
			$this->localtax2_tx		= $objp->localtax2_tx;
			$this->remise           = $objp->remise;
			$this->remise_percent   = $objp->remise_percent;
			$this->fk_product       = $objp->fk_product;
			$this->info_bits        = $objp->info_bits;
			$this->total_ht         = $objp->total_ht;
			$this->total_tva        = $objp->total_tva;
			$this->total_localtax1	= $objp->total_localtax1;
			$this->total_localtax2	= $objp->total_localtax2;
			$this->total_ttc        = $objp->total_ttc;
			$this->product_type     = $objp->product_type;

			$this->ref	            = $objp->product_ref;
			$this->product_libelle  = $objp->product_libelle;
			$this->product_desc     = $objp->product_desc;

			$this->date_start       = $this->db->jdate($objp->date_start);
			$this->date_end         = $this->db->jdate($objp->date_end);
			$this->fk_unit          = $objp->fk_unit;

			$this->db->free($result);
			return 1;
		}
		else
		{
			dol_print_error($this->db);
			return -1;
		}
	}
}
?>