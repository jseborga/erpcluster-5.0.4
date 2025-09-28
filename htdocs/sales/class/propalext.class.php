<?php

require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';

class Propalext extends Propal
{
	public $lines;
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


		$sql = "SELECT p.rowid, p.ref, p.remise, p.remise_percent, p.remise_absolue, p.fk_soc";
		$sql.= ", p.total, p.tva, p.localtax1, p.localtax2, p.total_ht";
		$sql.= ", p.datec";
		$sql.= ", p.date_valid as datev";
		$sql.= ", p.datep as dp";
		$sql.= ", p.fin_validite as dfv";
		$sql.= ", p.date_livraison as date_livraison";
		$sql.= ", p.model_pdf, p.ref_client, p.extraparams";
		$sql.= ", p.note_private, p.note_public";
		$sql.= ", p.fk_projet, p.fk_statut";
		$sql.= ", p.fk_user_author, p.fk_user_valid, p.fk_user_cloture";
		$sql.= ", p.fk_delivery_address";
		$sql.= ", p.fk_availability";
		$sql.= ", p.fk_input_reason";
		$sql.= ", p.fk_cond_reglement";
		$sql.= ", p.fk_mode_reglement";
		$sql.= ', p.fk_account';
		$sql.= ", p.fk_shipping_method";
		$sql.= ", p.fk_incoterms, p.location_incoterms";
		$sql.= ", p.fk_multicurrency, p.multicurrency_code, p.multicurrency_tx, p.multicurrency_total_ht, p.multicurrency_total_tva, p.multicurrency_total_ttc";
		$sql.= ", i.libelle as libelle_incoterms";
		$sql.= ", c.label as statut_label";
		$sql.= ", ca.code as availability_code, ca.label as availability";
		$sql.= ", dr.code as demand_reason_code, dr.label as demand_reason";
		$sql.= ", cr.code as cond_reglement_code, cr.libelle as cond_reglement, cr.libelle_facture as cond_reglement_libelle_doc";
		$sql.= ", cp.code as mode_reglement_code, cp.libelle as mode_reglement";
		$sql.= " FROM ".MAIN_DB_PREFIX."propal as p";
		$sql.= ' INNER JOIN '.MAIN_DB_PREFIX.'c_propalst as c ON p.fk_statut = c.id';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_paiement as cp ON p.fk_mode_reglement = cp.id';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_payment_term as cr ON p.fk_cond_reglement = cr.rowid';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_availability as ca ON p.fk_availability = ca.rowid';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_input_reason as dr ON p.fk_input_reason = dr.rowid';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_incoterms as i ON p.fk_incoterms = i.rowid';
		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
			$sql .= " AND p.entity IN (" . getEntity("conc", 1) . ")";
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
				$line = new Propal($this->db);


				$line->id                   = $obj->rowid;

				$line->ref                  = $obj->ref;
				$line->ref_client           = $obj->ref_client;
				$line->remise               = $obj->remise;
				$line->remise_percent       = $obj->remise_percent;
				$line->remise_absolue       = $obj->remise_absolue;
				$line->total                = $obj->total; // TODO deprecated
				$line->total_ht             = $obj->total_ht;
				$line->total_tva            = $obj->tva;
				$line->total_localtax1      = $obj->localtax1;
				$line->total_localtax2      = $obj->localtax2;
				$line->total_ttc            = $obj->total;
				$line->socid                = $obj->fk_soc;
				$line->fk_project           = $obj->fk_projet;
				$line->modelpdf             = $obj->model_pdf;
				$line->note                 = $obj->note_private; // TODO deprecated
				$line->note_private         = $obj->note_private;
				$line->note_public          = $obj->note_public;
				$line->statut               = $obj->fk_statut;
				$line->statut_libelle       = $obj->statut_label;

				$line->datec                = $this->db->jdate($obj->datec); // TODO deprecated
				$line->datev                = $this->db->jdate($obj->datev); // TODO deprecated
				$line->date_creation        = $this->db->jdate($obj->datec); //Creation date
				$line->date_validation      = $this->db->jdate($obj->datev); //Validation date
				$line->date                 = $this->db->jdate($obj->dp);   // Proposal date
				$line->datep                = $this->db->jdate($obj->dp);    // deprecated
				$line->fin_validite         = $this->db->jdate($obj->dfv);
				$line->date_livraison       = $this->db->jdate($obj->date_livraison);
				$line->shipping_method_id   = ($obj->fk_shipping_method>0)?$obj->fk_shipping_method:null;
				$line->availability_id      = $obj->fk_availability;
				$line->availability_code    = $obj->availability_code;
				$line->availability         = $obj->availability;
				$line->demand_reason_id     = $obj->fk_input_reason;
				$line->demand_reason_code   = $obj->demand_reason_code;
				$line->demand_reason        = $obj->demand_reason;
				$line->fk_address           = $obj->fk_delivery_address;

				$line->mode_reglement_id    = $obj->fk_mode_reglement;
				$line->mode_reglement_code  = $obj->mode_reglement_code;
				$line->mode_reglement       = $obj->mode_reglement;
				$line->fk_account           = ($obj->fk_account>0)?$obj->fk_account:null;
				$line->cond_reglement_id    = $obj->fk_cond_reglement;
				$line->cond_reglement_code  = $obj->cond_reglement_code;
				$line->cond_reglement       = $obj->cond_reglement;
				$line->cond_reglement_doc   = $obj->cond_reglement_libelle_doc;

				$line->extraparams          = (array) json_decode($obj->extraparams, true);

				$line->user_author_id = $obj->fk_user_author;
				$line->user_valid_id  = $obj->fk_user_valid;
				$line->user_close_id  = $obj->fk_user_cloture;

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

				if (
					$obj->fk_statut == self::STATUS_DRAFT)
				{
					$line->brouillon = 1;
				}


				// Retreive all extrafield for invoice
				// fetch optionals attributes and labels
				//require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
				//$extrafields=new ExtraFields($this->db);
				//$extralabels=$extrafields->fetch_name_optionals_label($this->table_element,true);
				//$this->fetch_optionals($this->id,$extralabels);



					//
					// Lignes propales liees a un produit ou non
					//
				$sql = "SELECT d.rowid, d.fk_propal, d.fk_parent_line, d.label as custom_label, d.description, d.price, d.vat_src_code, d.tva_tx, d.localtax1_tx, d.localtax2_tx, d.localtax1_type, d.localtax2_type, d.qty, d.fk_remise_except, d.remise_percent, d.subprice, d.fk_product,";
				$sql.= " d.info_bits, d.total_ht, d.total_tva, d.total_localtax1, d.total_localtax2, d.total_ttc, d.fk_product_fournisseur_price as fk_fournprice, d.buy_price_ht as pa_ht, d.special_code, d.rang, d.product_type,";
				$sql.= " d.fk_unit,";
				$sql.= ' p.ref as product_ref, p.description as product_desc, p.fk_product_type, p.label as product_label,';
				$sql.= ' d.date_start, d.date_end';
				$sql.= ' ,d.fk_multicurrency, d.multicurrency_code, d.multicurrency_subprice, d.multicurrency_total_ht, d.multicurrency_total_tva, d.multicurrency_total_ttc';
				$sql.= " FROM ".MAIN_DB_PREFIX."propaldet as d";
				$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product as p ON d.fk_product = p.rowid";
				$sql.= " WHERE d.fk_propal = ".$line->id;
				$sql.= " ORDER by d.rang";

				$result = $this->db->query($sql);
				if ($result)
				{
					//require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
					//$extrafieldsline=new ExtraFields($this->db);
					//$line = new PropaleLigne($this->db);
					//$extralabelsline=$extrafieldsline->fetch_name_optionals_label($line->table_element,true);

					$numdet = $this->db->num_rows($result);

					$i = 0;
					$lines = array();
					while ($i < $numdet)
					{
						$objp                   = $this->db->fetch_object($result);

						$row                   = new PropaleLigne($this->db);

						$row->rowid            = $objp->rowid; //Deprecated
						$row->id               = $objp->rowid;
						$row->fk_propal        = $objp->fk_propal;
						$row->fk_parent_line   = $objp->fk_parent_line;
						$row->product_type     = $objp->product_type;
						$row->label            = $objp->custom_label;
						$row->desc             = $objp->description;  // Description ligne
						$row->qty              = $objp->qty;
						$row->vat_src_code     = $objp->vat_src_code;
						$row->tva_tx           = $objp->tva_tx;
						$row->localtax1_tx     = $objp->localtax1_tx;
						$row->localtax2_tx     = $objp->localtax2_tx;
						$row->localtax1_type   = $objp->localtax1_type;
						$row->localtax2_type   = $objp->localtax2_type;
						$row->subprice         = $objp->subprice;
						$row->fk_remise_except = $objp->fk_remise_except;
						$row->remise_percent   = $objp->remise_percent;
						$row->price            = $objp->price;     // TODO deprecated

						$row->info_bits        = $objp->info_bits;
						$row->total_ht         = $objp->total_ht;
						$row->total_tva        = $objp->total_tva;
						$row->total_localtax1  = $objp->total_localtax1;
						$row->total_localtax2  = $objp->total_localtax2;
						$row->total_ttc        = $objp->total_ttc;
						$row->fk_fournprice    = $objp->fk_fournprice;
						$marginInfos            = getMarginInfos($objp->subprice, $objp->remise_percent, $objp->tva_tx, $objp->localtax1_tx, $objp->localtax2_tx, $row->fk_fournprice, $objp->pa_ht);
						$row->pa_ht            = $marginInfos[0];
						$row->marge_tx         = $marginInfos[1];
						$row->marque_tx        = $marginInfos[2];
						$row->special_code     = $objp->special_code;
						$row->rang             = $objp->rang;

						$row->fk_product       = $objp->fk_product;

						$row->ref              = $objp->product_ref;       // TODO deprecated
						$row->product_ref      = $objp->product_ref;
						$row->libelle          = $objp->product_label;     // TODO deprecated
						$row->product_label    = $objp->product_label;
						$row->product_desc     = $objp->product_desc;      // Description produit
						$row->fk_product_type  = $objp->fk_product_type;
						$row->fk_unit          = $objp->fk_unit;

						$row->date_start       = $this->db->jdate($objp->date_start);
						$row->date_end         = $this->db->jdate($objp->date_end);

						// Multicurrency
						$row->fk_multicurrency         = $objp->fk_multicurrency;
						$row->multicurrency_code       = $objp->multicurrency_code;
						$row->multicurrency_subprice   = $objp->multicurrency_subprice;
						$row->multicurrency_total_ht   = $objp->multicurrency_total_ht;
						$row->multicurrency_total_tva  = $objp->multicurrency_total_tva;
						$row->multicurrency_total_ttc  = $objp->multicurrency_total_ttc;

						$row->fetch_optionals($row->id,$extralabelsline);

						$lines[$i]        = $row;
						$i++;
					}
					$line->lines = $lines;
				}
				else
				{
					$this->error=$this->db->lasterror();
					return -1;
				}
				$this->lines[$obj->rowid] = $line;
			}
			$this->db->free($result);
			return $num;
		}
		else
		{
			$this->error=$this->db->lasterror();
			return -1;
		}
	}



	/**
	 *	Return clicable link of object (with eventually picto)
	 *
	 *	@param      int		$withpicto		Add picto into link
	 *	@param      string	$option			Where point the link ('expedition', 'document', ...)
	 *	@param      string	$get_params    	Parametres added to url
	 *	@return     string          		String with URL
	 */
	function getNomUrladd($withpicto=0,$option='', $get_params='')
	{
		global $langs, $conf;

		$result='';
		$label = '<u>' . $langs->trans("ShowPropal") . '</u>';
		if (! empty($this->ref))
			$label.= '<br><b>'.$langs->trans('Ref').':</b> '.$this->ref;
		if (! empty($this->ref_client))
			$label.= '<br><b>'.$langs->trans('RefCustomer').':</b> '.$this->ref_client;
		if (! empty($this->total_ht))
			$label.= '<br><b>' . $langs->trans('AmountHT') . ':</b> ' . price($this->total_ht, 0, $langs, 0, -1, -1, $conf->currency);
		if (! empty($this->total_tva))
			$label.= '<br><b>' . $langs->trans('VAT') . ':</b> ' . price($this->total_tva, 0, $langs, 0, -1, -1, $conf->currency);
		if (! empty($this->total_ttc))
			$label.= '<br><b>' . $langs->trans('AmountTTC') . ':</b> ' . price($this->total_ttc, 0, $langs, 0, -1, -1, $conf->currency);
		$linkclose = '" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip">';
		if ($option == '') {
			$link = '<a href="'.DOL_URL_ROOT.'/sales/propal/card.php?id='.$this->id. $get_params .$linkclose;
		}
		if ($option == 'compta') {  // deprecated
			$link = '<a href="'.DOL_URL_ROOT.'/comm/propal/card.php?id='.$this->id. $get_params .$linkclose;
		}
		if ($option == 'expedition') {
			$link = '<a href="'.DOL_URL_ROOT.'/expedition/propal.php?id='.$this->id. $get_params .$linkclose;
		}
		if ($option == 'document') {
			$link = '<a href="'.DOL_URL_ROOT.'/comm/propal/document.php?id='.$this->id. $get_params .$linkclose;
		}
		$linkend='</a>';

		$picto='propal';


		if ($withpicto)
			$result.=($link.img_object($label, $picto, 'class="classfortooltip"').$linkend);
		if ($withpicto && $withpicto != 2)
			$result.=' ';
		$result.=$link.$this->ref.$linkend;
		return $result;
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
		//print '<td class="linecolvat" align="right" width="50">'.$langs->trans('VAT').'</td>';

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

		// Total HT
		print '<td class="linecolht" align="right">'.$langs->trans('TotalHTShort').'</td>';

		// Multicurrency
		if (!empty($conf->multicurrency->enabled)) print '<td class="linecoltotalht_currency" align="right">'.$langs->trans('TotalHTShortCurrency').'</td>';

		print '<td class="linecoledit"></td>';  // No width to allow autodim

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
			$line->pu_ttc = $line->price;
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

	function addlineadd($desc, $pu_ht, $qty, $txtva, $txlocaltax1=0.0, $txlocaltax2=0.0, $fk_product=0, $remise_percent=0.0, $price_base_type='HT', $pu_ttc=0.0, $type=0, $info_bits=0, $notrigger=false, $date_start=null, $date_end=null, $array_options=0, $fk_unit=null,$lines)
	{
		global $langs,$mysoc,$conf;
		dol_syslog(get_class($this)."::addlineadd $desc,$pu_ht,$pu_ttc,$qty,$txtva,$fk_product,$remise_percent,$date_start,$date_end,$ventil,$info_bits,$price_base_type,$type", LOG_DEBUG);

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

					if (! empty($conf->global->STOCK_MUST_BE_ENOUGH_FOR_PROPOSAL) && $product_type == 0 && $product->stock_reel < $qty) {
						$langs->load("errors");
						$this->error=$langs->trans('ErrorStockIsNotEnoughToAddProductOnProposal', $product->ref);
						$this->db->rollback();
						return -3;
					}
				}

			// Calcul du total TTC et de la TVA pour la ligne a partir de
			// qty, pu, remise_percent et txtva
			// TRES IMPORTANT: C'est au moment de l'insertion ligne qu'on doit stocker
			// la part ht, tva et ttc, et ce au niveau de la ligne qui a son propre taux tva.

			//$localtaxes_type=getLocalTaxesFromRate($txtva,0,$this->thirdparty,$mysoc);
			$txtva = preg_replace('/\s*\(.*\)/','',$txtva);  // Remove code into vatrate.

			//$tabprice=calcul_price_total($qty, $pu, $remise_percent, $txtva, $txlocaltax1, $txlocaltax2, 0, $price_base_type, $info_bits, $product_type, $mysoc, $localtaxes_type, 100, $this->multicurrency_tx);

			$total_ht  = $tabprice[0];
			$total_tva = $tabprice[1];
			$total_ttc = $tabprice[2];
			$total_localtax1 = $tabprice[9];
			$total_localtax2 = $tabprice[10];

			$txtva = $lines->tva_tx;
			$total_ht  = $lines->total_ht+0;
			$total_tva = $lines->total_tva+0;
			$total_ttc = $lines->total_ttc+0;
			$total_localtax1 = $lines->total_localtax1+0;
			$total_localtax2 = $lines->total_localtax2+0;

			// MultiCurrency
			$multicurrency_total_ht  = $tabprice[16]+0;
			$multicurrency_total_tva = $tabprice[17]+0;
			$multicurrency_total_ttc = $tabprice[18]+0;
			$txlocaltax1=$lines->localtax1_tx;
			$txlocaltax2=$lines->localtax2_tx;

			$localtax1_type=$lines->localtax1_type;
			$localtax2_type=$lines->localtax2_type;
			$remise = $lines->remise;
			$remise_percent = $lines->remise_percent;
			$subprice = price2num($pu_ht,'MU');


			// Rang to use
			$rangtouse = $rang;
			if ($rangtouse == -1)
			{
				$rangmax = $this->line_max($fk_parent_line);
				$rangtouse = $rangmax + 1;
			}

			// TODO A virer
			// Anciens indicateurs: $price, $remise (a ne plus utiliser)
			//$price = $pu;
			//$remise = 0;
			if ($remise_percent > 0)
			{
				//$remise = round(($pu * $remise_percent / 100), 2);
				//$price = $pu - $remise;
			}

			// Insert line
			$this->line=new PropaleLigneext($this->db);

			$this->line->context = $this->context;

			$this->line->fk_propal=$this->id;
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
			$this->line->product_type=$product_type;
			$this->line->special_code=$special_code;
			$this->line->fk_parent_line=$fk_parent_line;
			$this->line->fk_unit=$fk_unit;

			$this->line->date_start=$date_start;
			$this->line->date_end=$date_end;

			$this->line->fk_fournprice = $fk_fournprice;
			$this->line->pa_ht = $pa_ht;

			$this->line->origin_id = $origin_id;
			$this->line->origin = $origin;

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
			$this->line->price=$pu_ttc;
			$this->line->remise=$remise;

			if (is_array($array_options) && count($array_options)>0) {
				$this->line->array_options=$array_options;
			}

			$result=$this->line->insertadd();
			if ($result > 0)
			{
				// Reorder if child line
				if (! empty($fk_parent_line)) $this->line_order(true,'DESC');

				// Mise a jour informations denormalisees au niveau de la propale meme
				//$result=$this->update_price(1,'auto',0,$mysoc);	// This method is designed to add line from user input so total calculation must be done using 'auto' mode.
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
	{

		dol_syslog(get_class($this)."::updateLineadd rowid=$rowid, pu=$pu, qty=$qty, remise_percent=$remise_percent,
			txtva=$txtva, desc=$desc, price_base_type=$price_base_type, info_bits=$info_bits, special_code=$special_code, fk_parent_line=$fk_parent_line, pa_ht=$pa_ht, type=$type, date_start=$date_start, date_end=$date_end");
		include_once DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php';

		// Clean parameters
		$remise_percent=price2num($remise_percent);
		$qty=price2num($qty);
		$subprice = price2num($lines->subprice);
		$price = price2num($lines->price);
		$txtva = price2num($txtva);
		$txlocaltax1=price2num($txlocaltax1);
		$txlocaltax2=price2num($txlocaltax2);
		$pa_ht=price2num($pa_ht);
		if (empty($qty) && empty($special_code)) $special_code=3;    // Set option tag
		if (! empty($qty) && $special_code == 3) $special_code=0;    // Remove option tag

		if ($this->statut == self::STATUS_DRAFT)
			{
				$this->db->begin();

			// Calcul du total TTC et de la TVA pour la ligne a partir de
			// qty, pu, remise_percent et txtva
			// TRES IMPORTANT: C'est au moment de l'insertion ligne qu'on doit stocker
			// la part ht, tva et ttc, et ce au niveau de la ligne qui a son propre taux tva.

			//$localtaxes_type=getLocalTaxesFromRate($txtva,0,$this->thirdparty,$mysoc);
			//$txtva = preg_replace('/\s*\(.*\)/','',$txtva);  // Remove code into vatrate.

			//$tabprice=calcul_price_total($qty, $pu, $remise_percent, $txtva, $txlocaltax1, $txlocaltax2, 0, $price_base_type, $info_bits, $type, $mysoc, $localtaxes_type, 100, $this->multicurrency_tx);
				$total_ht  = $tabprice[0];
				$total_tva = $tabprice[1];
				$total_ttc = $tabprice[2];
				$total_localtax1 = $tabprice[9];
				$total_localtax2 = $tabprice[10];

				$localtax1_type = $lines->localtax1_type;
				$localtax2_type = $lines->localtax2_type;

				$total_ht 	= $lines->total_ht;
				$total_tva 	= $lines->total_tva;
				$total_ttc 	= $lines->total_ttc;
				$remise 	= $lines->remise;
				$remise_percent = $lines->remise_percent;
				$fk_unit 	= $lines->fk_unit;
				$special_code = $lines->special_code;
				if (empty($price)) $price = $lines->price;
			// MultiCurrency
				$multicurrency_total_ht  = $tabprice[16];
				$multicurrency_total_tva = $tabprice[17];
				$multicurrency_total_ttc = $tabprice[18];

			// Anciens indicateurs: $price, $remise (a ne plus utiliser)
			//$price = $pu;
			//if ($remise_percent > 0)
			//{
			//   $remise = round(($pu * $remise_percent / 100), 2);
			//    $price = $pu - $remise;
			//}

			//Fetch current line from the database and then clone the object and set it in $oldline property
				$line = new PropaleLigneext($this->db);
				$line->fetch($rowid);

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
				$this->line->rowid				= $rowid;
				$this->line->label				= $label;
				$this->line->desc				= $desc;
				$this->line->qty				= $qty;
				$this->line->product_type			= $type;
				$this->line->tva_tx				= $txtva;
				$this->line->localtax1_tx		= $txlocaltax1;
				$this->line->localtax2_tx		= $txlocaltax2;
				$this->line->localtax1_type		= $localtax1_type;
				$this->line->localtax2_type		= $localtax2_type;
				$this->line->remise_percent		= $remise_percent;
				$this->line->subprice			= $subprice;
				$this->line->info_bits			= $info_bits;
				$this->line->total_ht			= $total_ht;
				$this->line->total_tva			= $total_tva;
				$this->line->total_localtax1	= $total_localtax1;
				$this->line->total_localtax2	= $total_localtax2;
				$this->line->total_ttc			= $total_ttc;
				$this->line->special_code		= $special_code;
				$this->line->fk_parent_line		= $fk_parent_line;
			//$this->line->skip_update_total	= $skip_update_total;
				$this->line->fk_unit	= $fk_unit;

				$this->line->fk_fournprice = $fk_fournprice;
			//$this->line->pa_ht = $pa_ht;

				$this->line->date_start=$date_start;
				$this->line->date_end=$date_end;

			// TODO deprecated
				$this->line->price=$price;
				$this->line->remise=$remise;

				if (is_array($array_options) && count($array_options)>0) {
					$this->line->array_options=$array_options;
				}

			// Multicurrency
			//$this->line->multicurrency_subprice		= price2num($pu * $this->multicurrency_tx);
			//$this->line->multicurrency_total_ht 	= $multicurrency_total_ht;
			//$this->line->multicurrency_total_tva 	= $multicurrency_total_tva;
			//$this->line->multicurrency_total_ttc 	= $multicurrency_total_ttc;

				$result=$this->line->updateadd();
				if ($result > 0)
				{
				// Reorder if child line
					if (! empty($fk_parent_line)) $this->line_order(true,'DESC');

					$this->update_price(1);

					$this->fk_propal = $this->id;
					$this->rowid = $rowid;

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
				dol_syslog(get_class($this)."::updateline Erreur -2 Propal en mode incompatible pour cette action");
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
		if (empty($this->total)) $this->total=0;
		if (empty($this->multicurrency_total_ht)) $this->multicurrency_total_ht=0;
		if (empty($this->multicurrency_total_tva)) $this->multicurrency_total_tva=0;
		if (empty($this->multicurrency_total_ttc)) $this->multicurrency_total_ttc=0;
		if (empty($this->remise)) $this->remise=0;	// TODO A virer

		$this->db->begin();

		// Mise a jour ligne en base
		$sql = "UPDATE ".MAIN_DB_PREFIX."propal SET";
		$sql.= " tva='".price2num($this->tva)."'";
		$sql.= " , localtax1=".price2num($this->localtax1);
		$sql.= " , localtax2=".price2num($this->localtax2);
		$sql.= " , total_ht=".price2num($this->total_ht);
		$sql.= " , total=".price2num($this->total);
		$sql.= " , multicurrency_total_ttc=".price2num($this->multicurrency_total_ttc);
		$sql.= " , multicurrency_total_ht=".price2num($this->multicurrency_total_ht);
		$sql.= " , multicurrency_total_tva=".price2num($this->multicurrency_total_tva);
		$sql.= " , remise=".price2num($this->remise)."";				// TODO A virer

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
	 * 	Retrieve an array of propal lines
	 *
	 * 	@return int		>0 if OK, <0 if KO
	 */
	function getLinesArrayadd()
	{
		// For other object, here we call fetch_lines. But fetch_lines does not exists on proposal

		$sql = 'SELECT pt.rowid, pt.label as custom_label, pt.description, pt.fk_product, pt.fk_remise_except,';
		$sql.= ' pt.qty, pt.tva_tx, pt.remise_percent, pt.remise, pt.subprice, pt.price, pt.info_bits,';
		$sql.= ' pt.total_ht, pt.total_tva, pt.total_ttc, pt.fk_product_fournisseur_price as fk_fournprice, pt.buy_price_ht as pa_ht, pt.special_code, pt.localtax1_tx, pt.localtax2_tx,';
		$sql.= ' pt.date_start, pt.date_end, pt.product_type, pt.rang, pt.fk_parent_line,';
		$sql.= ' pt.fk_unit,';
		$sql.= ' p.label as product_label, p.ref, p.fk_product_type, p.rowid as prodid,';
		$sql.= ' p.description as product_desc,';
		$sql.= ' p.entity';
		$sql.= ' ,pt.fk_multicurrency, pt.multicurrency_code, pt.multicurrency_subprice, pt.multicurrency_total_ht, pt.multicurrency_total_tva, pt.multicurrency_total_ttc';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'propaldet as pt';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON pt.fk_product=p.rowid';
		$sql.= ' WHERE pt.fk_propal = '.$this->id;
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

				$this->lines[$i]					= new PropaleLigne($this->db);
				$this->lines[$i]->id				= $obj->rowid; // for backward compatibility
				$this->lines[$i]->rowid				= $obj->rowid;
				$this->lines[$i]->label 			= $obj->custom_label;
				$this->lines[$i]->desc       		= $obj->description;
				$this->lines[$i]->description 		= $obj->description;
				$this->lines[$i]->fk_product		= $obj->fk_product;
				$this->lines[$i]->ref				= $obj->ref;
				$this->lines[$i]->product_ref		= $obj->ref;
				$this->lines[$i]->entity            = $obj->entity;             // Product entity
				$this->lines[$i]->product_label		= $obj->product_label;
				$this->lines[$i]->product_desc		= $obj->product_desc;
				$this->lines[$i]->fk_product_type	= $obj->fk_product_type;    // deprecated
				$this->lines[$i]->product_type		= $obj->product_type;
				$this->lines[$i]->qty				= $obj->qty;
				$this->lines[$i]->subprice			= $obj->subprice;
				$this->lines[$i]->price				= $obj->price;
				$this->lines[$i]->fk_remise_except 	= $obj->fk_remise_except;
				$this->lines[$i]->remise_percent	= $obj->remise_percent;
				$this->lines[$i]->remise			= $obj->remise;
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
				$this->lines[$i]->date_start		= $this->db->jdate($obj->date_start);
				$this->lines[$i]->date_end			= $this->db->jdate($obj->date_end);
				$this->lines[$i]->fk_unit			= $obj->fk_unit;

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
}

class PropaleLigneext extends PropaleLigne
{
	var $aData;
	function get_sum_taxes($id)
	{
		$sql = 'SELECT pd.rowid, pd.fk_propal, pd.fk_parent_line, pd.fk_product, pd.label as custom_label, pd.description, pd.price, pd.qty, pd.tva_tx,';
		$sql.= ' pd.remise, pd.remise_percent, pd.fk_remise_except, pd.subprice,';
		$sql.= ' pd.info_bits, pd.total_ht, pd.total_tva, pd.total_ttc, pd.fk_product_fournisseur_price as fk_fournprice, pd.buy_price_ht as pa_ht, pd.special_code, pd.rang,';
		$sql.= ' pd.fk_unit,';
		$sql.= ' pd.localtax1_tx, pd.localtax2_tx, pd.total_localtax1, pd.total_localtax2,';
		$sql.= ' pd.fk_multicurrency, pd.multicurrency_code, pd.multicurrency_subprice, pd.multicurrency_total_ht, pd.multicurrency_total_tva, pd.multicurrency_total_ttc,';
		$sql.= ' p.ref as product_ref, p.label as product_label, p.description as product_desc,';
		$sql.= ' pd.date_start, pd.date_end, pd.product_type';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'propaldet as pd';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON pd.fk_product = p.rowid';
		$sql.= ' WHERE pd.fk_propal = '.$id;

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
	 *  Insert object line propal in database
	 *
	 *	@param		int		$notrigger		1=Does not execute triggers, 0= execuete triggers
	 *	@return		int						<0 if KO, >0 if OK
	 */
	function insertadd($notrigger=0)
	{
		global $conf,$user;

		$error=0;

		dol_syslog(get_class($this)."::insert rang=".$this->rang);

		$pa_ht_isemptystring = (empty($this->pa_ht) && $this->pa_ht == ''); // If true, we can use a default value. If this->pa_ht = '0', we must use '0'.

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
		if (empty($this->remise_percent) || ! is_numeric($this->remise_percent)) $this->remise_percent=0;
		if (empty($this->info_bits)) $this->info_bits=0;
		if (empty($this->special_code)) $this->special_code=0;
		if (empty($this->fk_parent_line)) $this->fk_parent_line=0;
		if (empty($this->fk_fournprice)) $this->fk_fournprice=0;
		if (! is_numeric($this->qty)) $this->qty = 0;
		if (empty($this->pa_ht)) $this->pa_ht=0;
		if (empty($this->multicurrency_subprice))  $this->multicurrency_subprice=0;
		if (empty($this->multicurrency_total_ht))  $this->multicurrency_total_ht=0;
		if (empty($this->multicurrency_total_vat)) $this->multicurrency_total_vat=0;
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
		if ($this->product_type < 0) return -1;

		$this->db->begin();

		// Insert line into database
		$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'propaldet';
		$sql.= ' (fk_propal, fk_parent_line, label, description, fk_product, product_type,';
		$sql.= ' fk_remise_except, qty, tva_tx, localtax1_tx, localtax2_tx, localtax1_type, localtax2_type,';
		$sql.= ' subprice, remise_percent, remise, price, ';
		$sql.= ' info_bits, ';
		$sql.= ' total_ht, total_tva, total_localtax1, total_localtax2, total_ttc, fk_product_fournisseur_price, buy_price_ht, special_code, rang,';
		$sql.= ' fk_unit,';
		$sql.= ' date_start, date_end';
		$sql.= ', fk_multicurrency, multicurrency_code, multicurrency_subprice, multicurrency_total_ht, multicurrency_total_tva, multicurrency_total_ttc)';
		$sql.= " VALUES (".$this->fk_propal.",";
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
		$sql.= " ".price2num($this->remise).",";
		$sql.= " ".price2num($this->price).",";
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
		$sql.= ' '.(!$this->fk_unit ? 'NULL' : $this->fk_unit).',';
		$sql.= " ".(! empty($this->date_start)?"'".$this->db->idate($this->date_start)."'":"null").',';
		$sql.= " ".(! empty($this->date_end)?"'".$this->db->idate($this->date_end)."'":"null");
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
			$this->rowid=$this->db->last_insert_id(MAIN_DB_PREFIX.'propaldet');

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
				$result=$this->call_trigger('LINEPROPAL_INSERT',$user);
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
	 *	Update propal line object into DB
	 *
	 *	@param 	int		$notrigger	1=Does not execute triggers, 0= execute triggers
	 *	@return	int					<0 if ko, >0 if ok
	 */
		function updateadd($notrigger=0)
		{
			global $conf,$user;

			$error=0;

		$pa_ht_isemptystring = (empty($this->pa_ht) && $this->pa_ht == ''); // If true, we can use a default value. If this->pa_ht = '0', we must use '0'.

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
		if (empty($this->subprice)) $this->subprice=0;
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
		$sql = "UPDATE ".MAIN_DB_PREFIX."propaldet SET";
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
		$sql.= " , date_start=".(! empty($this->date_start)?"'".$this->db->idate($this->date_start)."'":"null");
		$sql.= " , date_end=".(! empty($this->date_end)?"'".$this->db->idate($this->date_end)."'":"null");
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
				$result=$this->call_trigger('LINEPROPAL_UPDATE',$user);
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
}
?>