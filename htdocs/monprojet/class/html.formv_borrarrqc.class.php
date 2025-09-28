<?php
class Formv extends Form
{
	/**
	 *  Return list of products for customer in Ajax if Ajax activated or go to select_produits_list
	 *
	 *  @param		int			$selected				Preselected products
	 *  @param		string		$htmlname				Name of HTML select field (must be unique in page)
	 *  @param		int			$filtertype				Filter on product type (''=nofilter, 0=product, 1=service)
	 *  @param		int			$limit					Limit on number of returned lines
	 *  @param		int			$price_level			Level of price to show
	 *  @param		int			$status					-1=Return all products, 0=Products not on sell, 1=Products on sell
	 *  @param		int			$finished				2=all, 1=finished, 0=raw material
	 *  @param		string		$selected_input_value	Value of preselected input text (with ajax)
	 *  @param		int			$hidelabel				Hide label (0=no, 1=yes, 2=show search icon (before) and placeholder, 3 search icon after)
	 *  @param		array		$ajaxoptions			Options for ajax_autocompleter
	 *  @param      int			$socid					Thirdparty Id
	 *  @return		void
	 */
	function select_produits_v($selected='', $htmlname='productid', $filtertype='', $limit=20, $price_level=0, $status=1, $finished=2, $selected_input_value='', $hidelabel=0, $ajaxoptions=array(),$socid=0,$action='',$filterstatic='')
	{
		global $langs,$conf;
		include_once(DOL_DOCUMENT_ROOT.'/monprojet/tpl/frames.tpl.php');
		$price_level = (! empty($price_level) ? $price_level : 0);

		if (! empty($conf->use_javascript_ajax) && ! empty($conf->global->PRODUIT_USE_SEARCH_TO_SELECT))
		{
			$placeholder='';
			if ($selected && empty($selected_input_value))
			{
				require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
				$product = new Product($this->db);
				$product->fetch($selected);
				$selected_input_value=$product->ref;
			}
			// mode=1 means customers products
			$urloption='htmlname='.$htmlname.'&outjson=1&price_level='.$price_level.'&type='.$filtertype.'&mode=1&status='.$status.'&finished='.$finished;
			//Price by customer
			if (! empty($conf->global->PRODUIT_CUSTOMER_PRICES) && !empty($socid)) {
				$urloption.='&socid='.$socid;
			}
			print ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/almacen/ajax/products.php', $urloption, $conf->global->PRODUIT_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
			if (empty($hidelabel)) print $langs->trans("RefOrLabel").' : ';
			else if ($hidelabel > 1) {
				if (! empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $placeholder=' placeholder="'.$langs->trans("RefOrLabel").'"';
				else $placeholder=' title="'.$langs->trans("RefOrLabel").'"';
				if ($hidelabel == 2) {
					print img_picto($langs->trans("Search"), 'search');
				}
			}
			print '<input type="text" class="theight20" size="10" name="search_'.$htmlname.'" id="search_'.$htmlname.'" value="'.$selected_input_value.'"'.$placeholder.' onblur="CambiaURLFrame(this.value);" '.($_SESSION['rf'] != 1?($action=='addpay'?'':'autofocus="autofocus"'):'').'/>';

			if ($hidelabel == 3) {
				print img_picto($langs->trans("Search"), 'search');
			}
		}
		else
		{
			print $this->select_produits_list_v($selected,$htmlname,$filtertype,$limit,$price_level,$filterkey,$status,$finished,0,$socid,$filterstatic);
		}
	}

    /**
     *	Return list of products for a customer
     *
     *	@param      int		$selected       Preselected product
     *	@param      string	$htmlname       Name of select html
     *  @param		string	$filtertype     Filter on product type (''=nofilter, 0=product, 1=service)
     *	@param      int		$limit          Limit on number of returned lines
     *	@param      int		$price_level    Level of price to show
     * 	@param      string	$filterkey      Filter on product
     *	@param		int		$status         -1=Return all products, 0=Products not on sell, 1=Products on sell
     *  @param      int		$finished       Filter on finished field: 2=No filter
     *  @param      int		$outputmode     0=HTML select string, 1=Array
     *  @param      int		$socid     		Thirdparty Id
     *  @return     array    				Array of keys for json
     */
    function select_produits_list_v($selected='',$htmlname='productid',$filtertype='',$limit=20,$price_level=0,$filterkey='',$status=1,$finished=2,$outputmode=0,$socid=0,$filterstatic='')
    {
        global $langs,$conf,$user,$db;

        $out='';
        $outarray=array();

        $sql = "SELECT ";
        $sql.= " p.rowid, p.label, p.ref, p.description, p.fk_product_type, p.price, p.price_ttc, p.price_base_type, p.tva_tx, p.duration, p.stock";

        //Price by customer
        if (! empty($conf->global->PRODUIT_CUSTOMER_PRICES) && !empty($socid)) {
        	$sql.=' ,pcp.rowid as idprodcustprice, pcp.price as custprice, pcp.price_ttc as custprice_ttc,';
        	$sql.=' pcp.price_base_type as custprice_base_type, pcp.tva_tx as custtva_tx';
        }

        // Multilang : we add translation
        if (! empty($conf->global->MAIN_MULTILANGS))
        {
            $sql.= ", pl.label as label_translated";
        }
		// Price by quantity
		if (! empty($conf->global->PRODUIT_CUSTOMER_PRICES_BY_QTY))
		{
			$sql.= ", (SELECT pp.rowid FROM ".MAIN_DB_PREFIX."product_price as pp WHERE pp.fk_product = p.rowid";
			if ($price_level >= 1 && !empty($conf->global->PRODUIT_MULTIPRICES)) $sql.= " AND price_level=".$price_level;
			$sql.= " ORDER BY date_price";
			$sql.= " DESC LIMIT 1) as price_rowid";
			$sql.= ", (SELECT pp.price_by_qty FROM ".MAIN_DB_PREFIX."product_price as pp WHERE pp.fk_product = p.rowid";
			if ($price_level >= 1 && !empty($conf->global->PRODUIT_MULTIPRICES)) $sql.= " AND price_level=".$price_level;
			$sql.= " ORDER BY date_price";
			$sql.= " DESC LIMIT 1) as price_by_qty";
		}
        $sql.= " FROM ".MAIN_DB_PREFIX."product as p";
        //Price by customer
        if (! empty($conf->global->PRODUIT_CUSTOMER_PRICES) && !empty($socid)) {
        	$sql.=" LEFT JOIN  ".MAIN_DB_PREFIX."product_customer_price as pcp ON pcp.fk_soc=".$socid." AND pcp.fk_product=p.rowid";
        }
        // Multilang : we add translation
        if (! empty($conf->global->MAIN_MULTILANGS))
        {
            $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product_lang as pl ON pl.fk_product = p.rowid AND pl.lang='". $langs->getDefaultLang() ."'";
        }
        $sql.= ' WHERE p.entity IN ('.getEntity('product', 1).')';
        if ($finished == 0)
        {
            $sql.= " AND p.finished = ".$finished;
        }
        elseif ($finished == 1)
        {
            $sql.= " AND p.finished = ".$finished;
            if ($status >= 0)  $sql.= " AND p.tosell = ".$status;
        }
        elseif ($status >= 0)
        {
            $sql.= " AND p.tosell = ".$status;
        }
        if (strval($filtertype) != '') $sql.=" AND p.fk_product_type=".$filtertype;
        // Add criteria on ref/label
        if ($filterkey != '')
        {
        	$sql.=' AND (';
        	$prefix=empty($conf->global->PRODUCT_DONOTSEARCH_ANYWHERE)?'%':'';	// Can use index if PRODUCT_DONOTSEARCH_ANYWHERE is on
            // For natural search
            $scrit = explode(' ', $filterkey);
            $i=0;
            if (count($scrit) > 1) $sql.="(";
            foreach ($scrit as $crit)
            {
            	if ($i > 0) $sql.=" AND ";
                $sql.="(p.ref LIKE '".$prefix.$crit."%' OR p.label LIKE '".$prefix.$crit."%' OR p.description LIKE '".$prefix.$crit."%'";
                if (! empty($conf->global->MAIN_MULTILANGS)) $sql.=" OR pl.label LIKE '".$prefix.$crit."%'";
                $sql.=")";
                $i++;
            }
            if (count($scrit) > 1) $sql.=")";
          	if (! empty($conf->barcode->enabled)) $sql.= " OR p.barcode LIKE '".$prefix.$filterkey."%'";
        	$sql.=')';
        }
        if ($filterstatic) $sql.= $filterstatic;
        $sql.= $db->order("p.ref");
        $sql.= $db->plimit($limit);

        // Build output string
        dol_syslog(get_class($this)."::select_produits_list_v search product sql=".$sql, LOG_DEBUG);
        $result=$this->db->query($sql);
        if ($result)
        {
            $num = $this->db->num_rows($result);

            $out.='<select class="flat" name="'.$htmlname.'" id="'.$htmlname.'">';
            $out.='<option value="0" selected="selected">&nbsp;</option>';

            $i = 0;
            while ($num && $i < $num)
            {
            	$opt = '';
				$optJson = array();
				$objp = $this->db->fetch_object($result);

				if (!empty($objp->price_by_qty) && $objp->price_by_qty == 1 && !empty($conf->global->PRODUIT_CUSTOMER_PRICES_BY_QTY))
				{ // Price by quantity will return many prices for the same product
					$sql = "SELECT rowid, quantity, price, unitprice, remise_percent, remise";
					$sql.= " FROM ".MAIN_DB_PREFIX."product_price_by_qty";
					$sql.= " WHERE fk_product_price=".$objp->price_rowid;
					$sql.= " ORDER BY quantity ASC";

					dol_syslog(get_class($this)."::select_produits_list_v search price by qty sql=".$sql);
					$result2 = $this->db->query($sql);
					if ($result2)
					{
						$nb_prices = $this->db->num_rows($result2);
						$j = 0;
						while ($nb_prices && $j < $nb_prices) {
							$objp2 = $this->db->fetch_object($result2);

							$objp->quantity = $objp2->quantity;
							$objp->price = $objp2->price;
							$objp->unitprice = $objp2->unitprice;
							$objp->remise_percent = $objp2->remise_percent;
							$objp->remise = $objp2->remise;
							$objp->price_by_qty_rowid = $objp2->rowid;

							$this->constructProductListOption_v($objp, $opt, $optJson, 0, $selected);

							$j++;

							// Add new entry
							// "key" value of json key array is used by jQuery automatically as selected value
							// "label" value of json key array is used by jQuery automatically as text for combo box
							$out.=$opt;
							array_push($outarray, $optJson);
						}
					}
				}
				else
				{
					$this->constructProductListOption_v($objp, $opt, $optJson, $price_level, $selected);
					// Add new entry
					// "key" value of json key array is used by jQuery automatically as selected value
					// "label" value of json key array is used by jQuery automatically as text for combo box
					$out.=$opt;
					array_push($outarray, $optJson);
				}

                $i++;
            }

            $out.='</select>';

            $this->db->free($result);

            if (empty($outputmode)) return $out;
            return $outarray;
        }
        else
		{
            dol_print_error($db);
        }
    }

	/**
	 *    Return list of categories having choosed type
	 *
	 *    @param	int		$type				Type de categories (0=product, 1=supplier, 2=customer, 3=member)
	 *    @param    string	$selected    		Id of category preselected or 'auto' (autoselect category if there is only one element)
	 *    @param    string	$htmlname			HTML field name
	 *    @param    int		$maxlength      	Maximum length for labels
	 *    @param    int		$excludeafterid 	Exclude all categories after this leaf in category tree.
	 *    @return	void
	 */
	function select_all_categories_submit($type, $selected='', $htmlname="parent", $maxlength=64, $excludeafterid=0)
	{
		global $langs;
		$langs->load("categories");

		$cat = new Categorie($this->db);
		$cate_arbo = $cat->get_full_arbo($type,$excludeafterid);

		$output = '<select class="flat" name="'.$htmlname.'" onchange="sendForm()">';
		if (is_array($cate_arbo))
		{
			if (! count($cate_arbo)) $output.= '<option value="-1" disabled="disabled">'.$langs->trans("NoCategoriesDefined").'</option>';
			else
			{
				$output.= '<option value="-1">&nbsp;</option>';
				foreach($cate_arbo as $key => $value)
				{
					if ($cate_arbo[$key]['id'] == $selected || ($selected == 'auto' && count($cate_arbo) == 1))
					{
						$add = 'selected="selected" ';
					}
					else
					{
						$add = '';
					}
					$output.= '<option '.$add.'value="'.$cate_arbo[$key]['id'].'">'.dol_trunc($cate_arbo[$key]['fulllabel'],$maxlength,'middle').'</option>';
				}
			}
		}
		$output.= '</select>';
		$output.= "\n";
		return $output;
	}

	/**
	 *	Return a HTML select string, built from an array of key+value.
	 *
	 *	@param	string	$htmlname       Name of html select area
	 *	@param	array	$array          Array with key+value
	 *	@param	string	$id             Preselected key
	 *	@param	int		$show_empty     1 si il faut ajouter une valeur vide dans la liste, 0 sinon
	 *	@param	int		$key_in_label   1 pour afficher la key dans la valeur "[key] value"
	 *	@param	int		$value_as_key   1 to use value as key
	 *	@param  string	$option         Valeur de l'option en fonction du type choisi
	 *	@param  int		$translate		Translate and encode value
	 * 	@param	int		$maxlen			Length maximum for labels
	 * 	@param	int		$disabled		Html select box is disabled
	 *  @param	int		$sort			'ASC' or 'DESC' =Sort on label, '' or 'NONE'=Do not sort
	 *  @param	string	$morecss		Add more class to css styles
	 * 	@return	string					HTML select string
	 */
	static function selectarrayv($htmlname, $array, $id='', $show_empty=0, $key_in_label=0, $value_as_key=0, $option='', $translate=0, $maxlen=0, $disabled=0, $sort='', $morecss='')
	{
		global $langs;

		if ($value_as_key) $array=array_combine($array, $array);

		$out='<select id="'.$htmlname.'" '.($disabled?'disabled="disabled" ':'').'class="flat1 theightv'.($morecss?' '.$morecss:'').'" name="'.$htmlname.'" '.($option != ''?$option:'').'>';

		if ($show_empty)
		{
			$out.='<option value="-1"'.($id==-1?' selected="selected"':'').'>&nbsp;</option>'."\n";
		}

		if (is_array($array))
		{
			// Translate
			if ($translate)
			{
				foreach($array as $key => $value) $array[$key]=$langs->trans($value);
			}

			// Sort
			if ($sort == 'ASC') asort($array);
			elseif ($sort == 'DESC') arsort($array);

			foreach($array as $key => $value)
			{
				$out.='<option value="'.$key.'"';
				if ($id != '' && $id == $key) $out.=' selected="selected"';		// To preselect a value
				$out.='>';

				if ($key_in_label)
				{
					$selectOptionValue = dol_htmlentitiesbr($key.' - '.($maxlen?dol_trunc($value,$maxlen):$value));
				}
				else
				{
					$selectOptionValue = dol_htmlentitiesbr($maxlen?dol_trunc($value,$maxlen):$value);
					if ($value == '' || $value == '-') $selectOptionValue='&nbsp;';
				}
				$out.=$selectOptionValue;
				$out.="</option>\n";
			}
		}

		$out.="</select>";
		return $out;
	}

    /**
     * constructProductListOption
     *
     * @param 	resultset	$objp			Resultset of fetch
     * @param 	string		$opt			Option
     * @param 	string		$optJson		Option
     * @param 	int			$price_level	Price level
     * @param 	string		$selected		Preselected value
     * @return	void
     */
	private function constructProductListOption_v(&$objp, &$opt, &$optJson, $price_level, $selected)
	{
		global $langs,$conf,$user,$db;

        $outkey='';
        $outval='';
        $outref='';
        $outlabel='';
        $outdesc='';
        $outtype='';
        $outprice_ht='';
        $outprice_ttc='';
        $outpricebasetype='';
        $outtva_tx='';
		$outqty=1;
		$outdiscount=0;

        $label=$objp->label;
        if (! empty($objp->label_translated)) $label=$objp->label_translated;
        if (! empty($filterkey) && $filterkey != '') $label=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$label,1);

        $outkey=$objp->rowid;
        $outref=$objp->ref;
        $outlabel=$objp->label;
        $outdesc=$objp->description;
        $outtype=$objp->fk_product_type;

        $opt = '<option value="'.$objp->rowid.'"';
        $opt.= ($objp->rowid == $selected)?' selected="selected"':'';
		$opt.= (!empty($objp->price_by_qty_rowid) && $objp->price_by_qty_rowid > 0)?' pbq="'.$objp->price_by_qty_rowid.'"':'';
        if (! empty($conf->stock->enabled) && $objp->fk_product_type == 0 && isset($objp->stock))
        {
			if ($objp->stock > 0) $opt.= ' class="product_line_stock_ok"';
			else if ($objp->stock <= 0) $opt.= ' class="product_line_stock_too_low"';
        }
        $opt.= '>';
        $opt.= $objp->ref.' - '.dol_trunc($label,32).' - ';
        $opt.= $objp->description.' - ';

        $objRef = $objp->ref;
        if (! empty($filterkey) && $filterkey != '') $objRef=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$objRef,1);
        $outval.=$objRef.' - '.dol_trunc($label,32).' - ';
        $outval.=$objDesc.' - ';

        $found=0;

        // Multiprice
        if ($price_level >= 1 && $conf->global->PRODUIT_MULTIPRICES)		// If we need a particular price level (from 1 to 6)
        {
            $sql = "SELECT price, price_ttc, price_base_type, tva_tx";
            $sql.= " FROM ".MAIN_DB_PREFIX."product_price";
            $sql.= " WHERE fk_product='".$objp->rowid."'";
            $sql.= " AND entity IN (".getEntity('productprice', 1).")";
            $sql.= " AND price_level=".$price_level;
            $sql.= " ORDER BY date_price";
            $sql.= " DESC LIMIT 1";

            dol_syslog(get_class($this).'::constructProductListOption_v search price for level '.$price_level.' sql='.$sql);
            $result2 = $this->db->query($sql);
            if ($result2)
            {
                $objp2 = $this->db->fetch_object($result2);
                if ($objp2)
                {
                    $found=1;
                    if ($user->rights->ventas->readprice)
                    {
                    if ($objp2->price_base_type == 'HT')
                    {
                        $opt.= price($objp2->price,1,$langs,0,0,-1,$conf->currency).' '.$langs->trans("HT");
                        $outval.= price($objp2->price,0,$langs,0,0,-1,$conf->currency).' '.$langs->transnoentities("HT");
                    }
                    else
                    {
                        $opt.= price($objp2->price_ttc,1,$langs,0,0,-1,$conf->currency).' '.$langs->trans("TTC");
                        $outval.= price($objp2->price_ttc,0,$langs,0,0,-1,$conf->currency).' '.$langs->transnoentities("TTC");
                    }
                	}
                    $outprice_ht=price($objp2->price);
                    $outprice_ttc=price($objp2->price_ttc);
                    $outpricebasetype=$objp2->price_base_type;
                    $outtva_tx=$objp2->tva_tx;
                }
            }
            else
            {
                dol_print_error($this->db);
            }
        }

		// Price by quantity
		if (!empty($objp->quantity) && $objp->quantity >= 1 && $conf->global->PRODUIT_CUSTOMER_PRICES_BY_QTY)
		{
			$found = 1;
			$outqty=$objp->quantity;
			$outdiscount=$objp->remise_percent;
			if ($user->rights->ventas->readprice)
			{
			if ($objp->quantity == 1)
			{
				$opt.= price($objp->unitprice,1,$langs,0,0,-1,$conf->currency)."/";
				$outval.= price($objp->unitprice,0,$langs,0,0,-1,$conf->currency)."/";
				$opt.= $langs->trans("Unit");	// Do not use strtolower because it breaks utf8 encoding
				$outval.=$langs->transnoentities("Unit");
			}
			else
			{
				$opt.= price($objp->price,1,$langs,0,0,-1,$conf->currency)."/".$objp->quantity;
				$outval.= price($objp->price,0,$langs,0,0,-1,$conf->currency)."/".$objp->quantity;
				$opt.= $langs->trans("Units");	// Do not use strtolower because it breaks utf8 encoding
				$outval.=$langs->transnoentities("Units");
			}
			}
			$outprice_ht=price($objp->unitprice);
            $outprice_ttc=price($objp->unitprice * (1 + ($objp->tva_tx / 100)));
            $outpricebasetype=$objp->price_base_type;
            $outtva_tx=$objp->tva_tx;
		}
		if (!empty($objp->quantity) && $objp->quantity >= 1)
		{
			if ($user->rights->ventas->readprice)
			{
			$opt.=" (".price($objp->unitprice,1,$langs,0,0,-1,$conf->currency)."/".$langs->trans("Unit").")";	// Do not use strtolower because it breaks utf8 encoding
			$outval.=" (".price($objp->unitprice,0,$langs,0,0,-1,$conf->currency)."/".$langs->transnoentities("Unit").")";	// Do not use strtolower because it breaks utf8 encoding
			}
		}
		if (!empty($objp->remise_percent) && $objp->remise_percent >= 1)
		{
			if ($user->rights->ventas->readprice)
			{
			$opt.=" - ".$langs->trans("Discount")." : ".vatrate($objp->remise_percent).' %';
			$outval.=" - ".$langs->transnoentities("Discount")." : ".vatrate($objp->remise_percent).' %';
			}
		}

		//Price by customer
		if (!empty($conf->global->PRODUIT_CUSTOMER_PRICES)) {
			if (!empty($objp->idprodcustprice)) {
				$found = 1;
				if ($user->rights->ventas->readprice)
				{			
				if ($objp->custprice_base_type == 'HT')
				{
					$opt.= price($objp->custprice,1,$langs,0,0,-1,$conf->currency).' '.$langs->trans("HT");
					$outval.= price($objp->custprice,0,$langs,0,0,-1,$conf->currency).' '.$langs->transnoentities("HT");
				}
				else
				{
					$opt.= price($objp->custprice_ttc,1,$langs,0,0,-1,$conf->currency).' '.$langs->trans("TTC");
					$outval.= price($objp->custprice_ttc,0,$langs,0,0,-1,$conf->currency).' '.$langs->transnoentities("TTC");
				}
				}	
				$outprice_ht=price($objp->custprice);
				$outprice_ttc=price($objp->custprice_ttc);
				$outpricebasetype=$objp->custprice_base_type;
				$outtva_tx=$objp->custtva_tx;
			}
		}

        // If level no defined or multiprice not found, we used the default price
        if (! $found)
        {
        	if ($user->rights->ventas->readprice)
			{
            if ($objp->price_base_type == 'HT')
            {
                $opt.= price($objp->price,1,$langs,0,0,-1,$conf->currency).' '.$langs->trans("HT");
                $outval.= price($objp->price,0,$langs,0,0,-1,$conf->currency).' '.$langs->transnoentities("HT");
            }
            else
            {
                $opt.= price($objp->price_ttc,1,$langs,0,0,-1,$conf->currency).' '.$langs->trans("TTC");
                $outval.= price($objp->price_ttc,0,$langs,0,0,-1,$conf->currency).' '.$langs->transnoentities("TTC");
            }
        	}
            $outprice_ht=price($objp->price);
            $outprice_ttc=price($objp->price_ttc);
            $outpricebasetype=$objp->price_base_type;
            $outtva_tx=$objp->tva_tx;
        }

        if (! empty($conf->stock->enabled) && isset($objp->stock) && $objp->fk_product_type == 0)
        {
            $opt.= ' - '.$langs->trans("Stock").':'.$objp->stock;
            $outval.=' - '.$langs->transnoentities("Stock").':'.$objp->stock;
        }

        if ($objp->duration)
        {
            $duration_value = substr($objp->duration,0,dol_strlen($objp->duration)-1);
            $duration_unit = substr($objp->duration,-1);
            if ($duration_value > 1)
            {
                $dur=array("h"=>$langs->trans("Hours"),"d"=>$langs->trans("Days"),"w"=>$langs->trans("Weeks"),"m"=>$langs->trans("Months"),"y"=>$langs->trans("Years"));
            }
            else
            {
                $dur=array("h"=>$langs->trans("Hour"),"d"=>$langs->trans("Day"),"w"=>$langs->trans("Week"),"m"=>$langs->trans("Month"),"y"=>$langs->trans("Year"));
            }
            $opt.= ' - '.$duration_value.' '.$langs->trans($dur[$duration_unit]);
            $outval.=' - '.$duration_value.' '.$langs->transnoentities($dur[$duration_unit]);
        }

        $opt.= "</option>\n";
		$optJson = array('key'=>$outkey, 'value'=>$outref, 'label'=>$outval, 'label2'=>$outlabel, 'desc'=>$outdesc, 'type'=>$outtype, 'price_ht'=>$outprice_ht, 'price_ttc'=>$outprice_ttc, 'pricebasetype'=>$outpricebasetype, 'tva_tx'=>$outtva_tx, 'qty'=>$outqty, 'discount'=>$outdiscount);
	}

    /**
     *    Affiche formulaire de selection des modes de reglement
     *
     *    @param    string  $page           Page
     *    @param    int     $selected       Id mode pre-selectionne
     *    @param    string  $htmlname       Name of select html field
     *    @param    string  $filtertype     To filter on field type in llx_c_paiement (array('code'=>xx,'label'=>zz))
     *    @return   void
     */
    function form_entrepot_sel($page, $selected='', $htmlname='entrepot_end_id', $filtertype='')
    {
        global $langs;
        if ($htmlname != "none")
        {
            print '<form method="POST" action="'.$page.'">';
            print '<input type="hidden" name="action" value="setmode">';
            print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
            print '<table class="nobordernopadding" cellpadding="0" cellspacing="0">';
            print '<tr><td>';
            print select_entrepot($selected,$htmlname,'');
            print '</td>';
            print '<td align="left"><input type="submit" class="button" value="'.$langs->trans("Modify").'"></td>';
            print '</tr></table></form>';
        }
        else
        {
            if ($selected)
            {
                print "&nbsp;";
            }
        }
    }
}


?>