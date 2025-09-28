<?php
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';

class Formext extends Form
{
	var $type_purchase_id;
	var $type_purchase_code;
	var $type_purchase_label;
	var $type_facture_id;
	var $type_facture_code;
	var $type_facture_label;

	/**
	 *  Return list of products for customer in Ajax if Ajax activated or go to select_produits_list
	 *
	 *  @param      int         $selected               Preselected products
	 *  @param      string      $htmlname               Name of HTML select field (must be unique in page)
	 *  @param      int         $filtertype             Filter on product type (''=nofilter, 0=product, 1=service)
	 *  @param      int         $limit                  Limit on number of returned lines
	 *  @param      int         $price_level            Level of price to show
	 *  @param      int         $status                 -1=Return all products, 0=Products not on sell, 1=Products on sell
	 *  @param      int         $finished               2=all, 1=finished, 0=raw material
	 *  @param      string      $selected_input_value   Value of preselected input text (with ajax)
	 *  @param      int         $hidelabel              Hide label (0=no, 1=yes, 2=show search icon (before) and placeholder, 3 search icon after)
	 *  @param      array       $ajaxoptions            Options for ajax_autocompleter
	 *  @param      int         $socid                  Thirdparty Id
	 *  @return     void
	 */
	function select_produits_v($selected='', $htmlname='productid', $filtertype='', $limit=20, $price_level=0, $status=1, $finished=2, $selected_input_value='', $hidelabel=0, $ajaxoptions=array(),$socid=0,$action='',$filterstatic='')
	{
		global $langs,$conf;

		include_once(DOL_DOCUMENT_ROOT.'/purchase/tpl/frames.tpl.php');
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
			print ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/purchase/ajax/products.php', $urloption, $conf->global->PRODUIT_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
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
	 *  Return list of products for a customer
	 *
	 *  @param      int     $selected       Preselected product
	 *  @param      string  $htmlname       Name of select html
	 *  @param      string  $filtertype     Filter on product type (''=nofilter, 0=product, 1=service)
	 *  @param      int     $limit          Limit on number of returned lines
	 *  @param      int     $price_level    Level of price to show
	 *  @param      string  $filterkey      Filter on product
	 *  @param      int     $status         -1=Return all products, 0=Products not on sell, 1=Products on sell
	 *  @param      int     $finished       Filter on finished field: 2=No filter
	 *  @param      int     $outputmode     0=HTML select string, 1=Array
	 *  @param      int     $socid          Thirdparty Id
	 *  @return     array                   Array of keys for json
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
			$prefix=empty($conf->global->PRODUCT_DONOTSEARCH_ANYWHERE)?'%':'';  // Can use index if PRODUCT_DONOTSEARCH_ANYWHERE is on
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
	 * constructProductListOption
	 *
	 * @param   resultset   $objp           Resultset of fetch
	 * @param   string      $opt            Option
	 * @param   string      $optJson        Option
	 * @param   int         $price_level    Price level
	 * @param   string      $selected       Preselected value
	 * @return  void
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
		if ($price_level >= 1 && $conf->global->PRODUIT_MULTIPRICES)    
		    // If we need a particular price level (from 1 to 6)
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
					$opt.= $langs->trans("Unit"); 
				  // Do not use strtolower because it breaks utf8 encoding
					$outval.=$langs->transnoentities("Unit");
				}
				else
				{
					$opt.= price($objp->price,1,$langs,0,0,-1,$conf->currency)."/".$objp->quantity;
					$outval.= price($objp->price,0,$langs,0,0,-1,$conf->currency)."/".$objp->quantity;
					$opt.= $langs->trans("Units"); 
				 // Do not use strtolower because it breaks utf8 encoding
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
				$opt.=" (".price($objp->unitprice,1,$langs,0,0,-1,$conf->currency)."/".$langs->trans("Unit").")";  
			 // Do not use strtolower because it breaks utf8 encoding
				$outval.=" (".price($objp->unitprice,0,$langs,0,0,-1,$conf->currency)."/".$langs->transnoentities("Unit").")"; 
			 // Do not use strtolower because it breaks utf8 encoding
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
	 *  Output an HTML select vat rate
	 *
	 *  @param	string	$htmlname           Nom champ html
	 *  @param  float	$selectedrate       Forcage du taux tva pre-selectionne. Mettre '' pour aucun forcage.
	 *  @param  Societe	$societe_vendeuse   Objet societe vendeuse
	 *  @param  Societe	$societe_acheteuse  Objet societe acheteuse
	 *  @param  int		$idprod             Id product
	 *  @param  int		$info_bits          Miscellaneous information on line (1 for NPR)
	 *  @param  int		$type               ''=Unknown, 0=Product, 1=Service (Used if idprod not defined)
	 *                  					Si vendeur non assujeti a TVA, TVA par defaut=0. Fin de regle.
	 *                  					Si le (pays vendeur = pays acheteur) alors la TVA par defaut=TVA du produit vendu. Fin de regle.
	 *                  					Si (vendeur et acheteur dans Communaute europeenne) et bien vendu = moyen de transports neuf (auto, bateau, avion), TVA par defaut=0 (La TVA doit etre paye par l'acheteur au centre d'impots de son pays et non au vendeur). Fin de regle.
	 *                                      Si vendeur et acheteur dans Communauté européenne et acheteur= particulier alors TVA par défaut=TVA du produit vendu. Fin de règle.
	 *                                      Si vendeur et acheteur dans Communauté européenne et acheteur= entreprise alors TVA par défaut=0. Fin de règle.
	 *                  					Sinon la TVA proposee par defaut=0. Fin de regle.
	 *  @param	bool	$options_only		Return options only (for ajax treatment)
	 *  @return	string
	 */
	function load_tvaadd($htmlname='tauxtva', $selectedrate='', $societe_vendeuse='', $societe_acheteuse='', $idprod=0, $info_bits=0, $type='', $options_only=false)
	{
		global $langs,$conf,$mysoc;

		$return='';
		$txtva=array();
		$libtva=array();
		$nprtva=array();

		// Define defaultnpr and defaultttx
		$defaultnpr=($info_bits & 0x01);
		$defaultnpr=(preg_match('/\*/',$selectedrate) ? 1 : $defaultnpr);
		$defaulttx=str_replace('*','',$selectedrate);

		// Check parameters
		if (is_object($societe_vendeuse) && ! $societe_vendeuse->country_code)
		{
			if ($societe_vendeuse->id == $mysoc->id)
			{
				$return.= '<font class="error">'.$langs->trans("ErrorYourCountryIsNotDefined").'</div>';
			}
			else
			{
				$return.= '<font class="error">'.$langs->trans("ErrorSupplierCountryIsNotDefined").'</div>';
			}
			return $return;
		}

		//var_dump($societe_acheteuse);
		//print "name=$name, selectedrate=$selectedrate, seller=".$societe_vendeuse->country_code." buyer=".$societe_acheteuse->country_code." buyer is company=".$societe_acheteuse->isACompany()." idprod=$idprod, info_bits=$info_bits type=$type";
		//exit;

		// Define list of countries to use to search VAT rates to show
		// First we defined code_country to use to find list
		if (is_object($societe_vendeuse))
		{
			$code_country="'".$societe_vendeuse->country_code."'";
		}
		else
		{
			$code_country="'".$mysoc->country_code."'";   // Pour compatibilite ascendente
		}
		if (! empty($conf->global->SERVICE_ARE_ECOMMERCE_200238EC))    // If option to have vat for end customer for services is on
		{
			if (! $societe_vendeuse->isInEEC() && (! is_object($societe_acheteuse) || ($societe_acheteuse->isInEEC() && ! $societe_acheteuse->isACompany())))
			{
				// We also add the buyer
				if (is_numeric($type))
				{
					if ($type == 1) // We know product is a service
					{
						$code_country.=",'".$societe_acheteuse->country_code."'";
					}
				}
				else if (! $idprod)  // We don't know type of product
				{
					$code_country.=",'".$societe_acheteuse->country_code."'";
				}
				else
				{
					$prodstatic=new Product($this->db);
					$prodstatic->fetch($idprod);
					if ($prodstatic->type == 1)   // We know product is a service
					{
						$code_country.=",'".$societe_acheteuse->country_code."'";
					}
				}
			}
		}

		// Now we get list
		$num = $this->load_cache_vatratesadd($code_country);
		if ($num > 0)
		{
			// Definition du taux a pre-selectionner (si defaulttx non force et donc vaut -1 ou '')
			if ($defaulttx < 0 || dol_strlen($defaulttx) == 0)
			{
				$defaulttx=get_default_tva($societe_vendeuse,$societe_acheteuse,$idprod);
				$defaultnpr=get_default_npr($societe_vendeuse,$societe_acheteuse,$idprod);
			}

			// Si taux par defaut n'a pu etre determine, on prend dernier de la liste.
			// Comme ils sont tries par ordre croissant, dernier = plus eleve = taux courant
			if ($defaulttx < 0 || dol_strlen($defaulttx) == 0)
			{
				if (empty($conf->global->MAIN_VAT_DEFAULT_IF_AUTODETECT_FAILS)) $defaulttx = $this->cache_vatrates[$num-1]['txtva'];
				else $defaulttx=($conf->global->MAIN_VAT_DEFAULT_IF_AUTODETECT_FAILS == 'none' ? '' : $conf->global->MAIN_VAT_DEFAULT_IF_AUTODETECT_FAILS);
			}

			// Disabled if seller is not subject to VAT
			$disabled=false; $title='';
			if (is_object($societe_vendeuse) && $societe_vendeuse->id == $mysoc->id && $societe_vendeuse->tva_assuj == "0")
			{
				$title=' title="'.$langs->trans('VATIsNotUsed').'"';
				$disabled=true;
			}

			if (! $options_only) $return.= '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'"'.($disabled?' disabled="disabled"':'').$title.'>';

			foreach ($this->cache_vatrates as $rate)
			{
				// Keep only 0 if seller is not subject to VAT
				if ($disabled && $rate['txtva'] != 0) continue;

				$return.= '<option value="'.$rate['txtva'];
				$return.= $rate['nprtva'] ? '*': '';
				$return.= '"';
				if ($rate['txtva'] == $defaulttx && $rate['nprtva'] == $defaultnpr)
				{
					$return.= ' selected="selected"';
				}
				$return.= '>'.vatrate($rate['libtva']);
				$return.= $rate['nprtva'] ? ' *': '';
				$return.= $rate['labeltva'] ? ' '.$rate['labeltva']: '';
				
				$return.= '</option>';

				$this->tva_taux_value[]		= $rate['txtva'];
				$this->tva_taux_libelle[]	= $rate['libtva'];
				$this->tva_taux_npr[]		= $rate['nprtva'];
			}

			if (! $options_only) $return.= '</select>';
		}
		else
		{
			$return.= $this->error;
		}

		$this->num = $num;
		return $return;
	}

	/**
	 *  Load into the cache vat rates of a country
	 *
	 *  @param  string  $country_code       Country code
	 *  @return int                         Nb of loaded lines, 0 if already loaded, <0 if KO
	 */
	function load_cache_vatratesadd($country_code)
	{
		global $langs;

		$num = count($this->cache_vatrates);
		if ($num > 0) return $num;    // Cache deja charge

		$sql  = "SELECT DISTINCT t.taux, t.recuperableonly, t.note";
		$sql.= " FROM ".MAIN_DB_PREFIX."c_tva_local as t, ".MAIN_DB_PREFIX."c_country as c";
		$sql.= " WHERE t.fk_pays = c.rowid";
		$sql.= " AND t.active = 1";
		$sql.= " AND c.code IN (".$country_code.")";
		$sql.= " ORDER BY t.taux ASC, t.recuperableonly ASC";

		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				for ($i = 0; $i < $num; $i++)
				{
					$obj = $this->db->fetch_object($resql);
					$this->cache_vatrates[$i]['txtva']  = $obj->taux;
					$this->cache_vatrates[$i]['libtva'] = $obj->taux.'%';
					$this->cache_vatrates[$i]['nprtva'] = $obj->recuperableonly;
					$this->cache_vatrates[$i]['labeltva'] = $obj->note;
				}

				return $num;
			}
			else
			{
				$this->error = '<font class="error">'.$langs->trans("ErrorNoVATRateDefinedForSellerCountry",$country_code).'</font>';
				return -1;
			}
		}
		else
		{
			$this->error = '<font class="error">'.$this->db->error().'</font>';
			return -2;
		}
	}

	function load_type_tva($htmlname='type_tva', $selectedrate='', $societe_vendeuse='', $societe_acheteuse='', $idprod=0, $info_bits=0, $type='', $options_only=false)
	{
		global $langs,$conf,$mysoc;

		$return='';
		$txtva=array();
		$libtva=array();
		$nprtva=array();

		// Define defaultnpr and defaultttx
		$defaultnpr=($info_bits & 0x01);
		$defaultnpr=(preg_match('/\*/',$selectedrate) ? 1 : $defaultnpr);
		$defaulttx=str_replace('*','',$selectedrate);

		// Check parameters
		if (is_object($societe_vendeuse) && ! $societe_vendeuse->country_code)
		{
			if ($societe_vendeuse->id == $mysoc->id)
			{
				$return.= '<font class="error">'.$langs->trans("ErrorYourCountryIsNotDefined").'</div>';
			}
			else
			{
				$return.= '<font class="error">'.$langs->trans("ErrorSupplierCountryIsNotDefined").'</div>';
			}
			return $return;
		}

		//var_dump($societe_acheteuse);
		//print "name=$name, selectedrate=$selectedrate, seller=".$societe_vendeuse->country_code." buyer=".$societe_acheteuse->country_code." buyer is company=".$societe_acheteuse->isACompany()." idprod=$idprod, info_bits=$info_bits type=$type";
		//exit;

		// Define list of countries to use to search VAT rates to show
		// First we defined code_country to use to find list
		if (is_object($societe_vendeuse))
		{
			$code_country="'".$societe_vendeuse->country_code."'";
		}
		else
		{
			$code_country="'".$mysoc->country_code."'";   // Pour compatibilite ascendente
		}
		if (! empty($conf->global->SERVICE_ARE_ECOMMERCE_200238EC))    // If option to have vat for end customer for services is on
		{
			if (! $societe_vendeuse->isInEEC() && (! is_object($societe_acheteuse) || ($societe_acheteuse->isInEEC() && ! $societe_acheteuse->isACompany())))
			{
				// We also add the buyer
				if (is_numeric($type))
				{
					if ($type == 1) // We know product is a service
					{
						$code_country.=",'".$societe_acheteuse->country_code."'";
					}
				}
				else if (! $idprod)  // We don't know type of product
				{
					$code_country.=",'".$societe_acheteuse->country_code."'";
				}
				else
				{
					$prodstatic=new Product($this->db);
					$prodstatic->fetch($idprod);
					if ($prodstatic->type == 1)   // We know product is a service
					{
						$code_country.=",'".$societe_acheteuse->country_code."'";
					}
				}
			}
		}

		// Now we get list
		$num = $this->load_cache_typeratesadd($code_country);
		if ($num > 0)
		{
			// Definition du taux a pre-selectionner (si defaulttx non force et donc vaut -1 ou '')
			if ($defaulttx < 0 || dol_strlen($defaulttx) == 0)
			{
				$defaulttx=get_default_tva($societe_vendeuse,$societe_acheteuse,$idprod);
				$defaultnpr=get_default_npr($societe_vendeuse,$societe_acheteuse,$idprod);
			}

			// Si taux par defaut n'a pu etre determine, on prend dernier de la liste.
			// Comme ils sont tries par ordre croissant, dernier = plus eleve = taux courant
			if ($defaulttx < 0 || dol_strlen($defaulttx) == 0)
			{
				if (empty($conf->global->MAIN_VAT_DEFAULT_IF_AUTODETECT_FAILS)) $defaulttx = $this->cache_vatrates[$num-1]['txtva'];
				else $defaulttx=($conf->global->MAIN_VAT_DEFAULT_IF_AUTODETECT_FAILS == 'none' ? '' : $conf->global->MAIN_VAT_DEFAULT_IF_AUTODETECT_FAILS);
			}

			// Disabled if seller is not subject to VAT
			$disabled=false; $title='';
			if (is_object($societe_vendeuse) && $societe_vendeuse->id == $mysoc->id && $societe_vendeuse->tva_assuj == "0")
			{
				$title=' title="'.$langs->trans('VATIsNotUsed').'"';
				$disabled=true;
			}

			if (! $options_only) $return.= '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'"'.($disabled?' disabled="disabled"':'').$title.'>';

			foreach ($this->cache_vatrates as $rate)
			{
				// Keep only 0 if seller is not subject to VAT
				if ($disabled && $rate['idtva'] != 0) continue;

				$return.= '<option value="'.$rate['idtva'];
				$return.= $rate['nprtva'] ? '*': '';
				$return.= '"';
				if ($rate['idtva'] == $defaulttx && $rate['nprtva'] == $defaultnpr)
				{
					$return.= ' selected="selected"';
				}
				$return.= '>'.$rate['libtva'];
				//$return.= $rate['nprtva'] ? ' *': '';
				$return.= $rate['labeltva'] ? ' '.$rate['labeltva']: '';
				
				$return.= '</option>';

				$this->tva_taux_value[]     = $rate['idtva'];
				$this->tva_taux_libelle[]   = $rate['libtva'];
				$this->tva_taux_npr[]       = $rate['nprtva'];
			}

			if (! $options_only) $return.= '</select>';
		}
		else
		{
			$return.= $this->error;
		}

		$this->num = $num;
		return $return;
	}

	/**
	 *  Load into the cache vat rates of a country
	 *
	 *  @param  string  $country_code       Country code
	 *  @return int                         Nb of loaded lines, 0 if already loaded, <0 if KO
	 */
	function load_cache_typeratesadd($country_code)
	{
		global $langs;

		$num = count($this->cache_vatrates);
		if ($num > 0) return $num;    // Cache deja charge

		$sql  = "SELECT DISTINCT t.rowid, t.code, t.recuperableonly, t.note";
		$sql.= " FROM ".MAIN_DB_PREFIX."c_tva_local as t, ".MAIN_DB_PREFIX."c_country as c";
		$sql.= " WHERE t.fk_pays = c.rowid";
		$sql.= " AND t.active = 1";
		$sql.= " AND c.code IN (".$country_code.")";
		$sql.= " ORDER BY t.note ASC, t.recuperableonly ASC";

		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				for ($i = 0; $i < $num; $i++)
				{
					$obj = $this->db->fetch_object($resql);
					$this->cache_vatrates[$i]['idtva']  = $obj->rowid;
					$this->cache_vatrates[$i]['libtva'] = $obj->code;
					$this->cache_vatrates[$i]['nprtva'] = $obj->recuperableonly;
					$this->cache_vatrates[$i]['labeltva'] = $obj->note;
				}

				return $num;
			}
			else
			{
				$this->error = '<font class="error">'.$langs->trans("ErrorNoVATRateDefinedForSellerCountry",$country_code).'</font>';
				return -1;
			}
		}
		else
		{
			$this->error = '<font class="error">'.$this->db->error().'</font>';
			return -2;
		}
	}

	/**
	 *  Return list of products for customer (in Ajax if Ajax activated or go to select_produits_fournisseurs_list)
	 *
	 *  @param  int     $socid          Id third party
	 *  @param  string  $selected       Preselected product
	 *  @param  string  $htmlname       Name of HTML Select
	 *  @param  string  $filtertype     Filter on product type (''=nofilter, 0=product, 1=service)
	 *  @param  string  $filtre         For a SQL filter
	 *  @param  array   $ajaxoptions    Options for ajax_autocompleter
	 *  @param  int     $hidelabel      Hide label (0=no, 1=yes)
	 *  @return void
	 */
	function select_produits_fournisseurs_c($socid, $selected='', $htmlname='productid', $filtertype='', $filtre='', $ajaxoptions=array(), $hidelabel=0)
	{
		global $langs,$conf;
		global $price_level, $status, $finished;
		include_once(DOL_DOCUMENT_ROOT.'/purchase/tpl/framesfourn.tpl.php');
		if (! empty($conf->use_javascript_ajax) && ! empty($conf->global->PRODUIT_USE_SEARCH_TO_SELECT))
		{
			// mode=2 means suppliers products
			$urloption=($socid > 0?'socid='.$socid.'&':'').'htmlname='.$htmlname.'&outjson=1&price_level='.$price_level.'&type='.$filtertype.'&mode=2&status='.$status.'&finished='.$finished;
			print ajax_autocompleter('', $htmlname, DOL_URL_ROOT.'/product/ajax/products.php', $urloption, $conf->global->PRODUIT_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
			print ($hidelabel?'':$langs->trans("RefOrLabel").' : ').'<input type="text" size="16" name="search_'.$htmlname.'" id="search_'.$htmlname.'" onblur="CambiaURLFrame(this.value);">';
		}
		else
		{
			print $this->select_produits_fournisseurs_list_c($socid,$selected,$htmlname,$filtertype,$filtre,'',-1,0);
		}
	}

	/**
	 *  Return list of suppliers products
	 *
	 *  @param  int     $socid          Id societe fournisseur (0 pour aucun filtre)
	 *  @param  int     $selected       Produit pre-selectionne
	 *  @param  string  $htmlname       Nom de la zone select
	 *  @param  string  $filtertype     Filter on product type (''=nofilter, 0=product, 1=service)
	 *  @param  string  $filtre         Pour filtre sql
	 *  @param  string  $filterkey      Filtre des produits
	 *  @param  int     $statut         -1=Return all products, 0=Products not on sell, 1=Products on sell
	 *  @param  int     $outputmode     0=HTML select string, 1=Array
	 *  @param  int     $limit          Limit of line number
	 *  @return array                   Array of keys for json
	 */
	function select_produits_fournisseurs_list_c($socid,$selected='',$htmlname='productid',$filtertype='',$filtre='',$filterkey='',$statut=-1,$outputmode=0,$limit=100)
	{
		global $langs,$conf,$db;
		

		$out='';
		$outarray=array();

		$langs->load('stocks');

		$sql = "SELECT p.rowid, p.label, p.ref, p.price, p.duration,";
		$sql.= " pfp.ref_fourn, pfp.rowid as idprodfournprice, pfp.price as fprice, pfp.quantity, pfp.remise_percent, pfp.remise, pfp.unitprice,";
		$sql.= " s.nom as name";
		$sql.= " FROM ".MAIN_DB_PREFIX."product as p";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product_fournisseur_price as pfp ON p.rowid = pfp.fk_product";
		if ($socid) $sql.= " AND pfp.fk_soc = ".$socid;
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON pfp.fk_soc = s.rowid";
		$sql.= " WHERE p.entity IN (".getEntity('product', 1).")";
		$sql.= " AND p.tobuy = 1";
		if (strval($filtertype) != '') $sql.=" AND p.fk_product_type=".$this->db->escape($filtertype);
		if (! empty($filtre)) $sql.=" ".$filtre;
		// Add criteria on ref/label
		if ($filterkey != '')
		{
			$sql.=' AND (';
			$prefix=empty($conf->global->PRODUCT_DONOTSEARCH_ANYWHERE)?'%':'';  // Can use index if PRODUCT_DONOTSEARCH_ANYWHERE is on
			// For natural search
			$scrit = explode(' ', $filterkey);
			$i=0;
			if (count($scrit) > 1) $sql.="(";
			foreach ($scrit as $crit)
			{
				if ($i > 0) $sql.=" AND ";
				$sql.="(pfp.ref_fourn LIKE '".$this->db->escape($prefix.$crit)."%' OR p.ref LIKE '".$this->db->escape($prefix.$crit)."%' OR p.label LIKE '".$this->db->escape($prefix.$crit)."%')";
				$i++;
			}
			if (count($scrit) > 1) $sql.=")";
			if (! empty($conf->barcode->enabled)) $sql.= " OR p.barcode LIKE '".$this->db->escape($prefix.$filterkey)."%'";
			$sql.=')';
		}
		$sql.= " ORDER BY pfp.ref_fourn DESC, pfp.quantity ASC";
		$sql.= $db->plimit($limit);

		// Build output string

		dol_syslog(get_class($this)."::select_produits_fournisseurs_list_c", LOG_DEBUG);
		$result=$this->db->query($sql);
		if ($result)
		{

			$num = $this->db->num_rows($result);

			//$out.='<select class="flat" id="select'.$htmlname.'" name="'.$htmlname.'">';  // remove select to have id same with combo and ajax
			$out.='<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'">';
			if (! $selected) $out.='<option value="0" selected="selected">&nbsp;</option>';
			else $out.='<option value="0">&nbsp;</option>';

			$i = 0;
			while ($i < $num)
			{
				$objp = $this->db->fetch_object($result);

				$outkey=$objp->idprodfournprice;
				$outref=$objp->ref;
				$outval='';
				$outqty=1;
				$outdiscount=0;

				$opt = '<option value="'.$objp->idprodfournprice.'"';
				if ($selected && $selected == $objp->idprodfournprice) $opt.= ' selected="selected"';
				if (empty($objp->idprodfournprice)) $opt.=' disabled="disabled"';
				$opt.= '>';

				$objRef = $objp->ref;
				if ($filterkey && $filterkey != '') $objRef=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$objRef,1);
				$objRefFourn = $objp->ref_fourn;
				if ($filterkey && $filterkey != '') $objRefFourn=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$objRefFourn,1);
				$label = $objp->label;
				if ($filterkey && $filterkey != '') $label=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$label,1);

				$opt.=$objp->ref;
				if (! empty($objp->idprodfournprice)) $opt.=' ('.$objp->ref_fourn.')';
				$opt.=' - ';
				$outval.=$objRef;
				if (! empty($objp->idprodfournprice)) $outval.=' ('.$objRefFourn.')';
				$outval.=' - ';
				$opt.=dol_trunc($objp->label,18).' - ';
				$outval.=dol_trunc($label,18).' - ';

				if (! empty($objp->idprodfournprice))
				{
					$outqty=$objp->quantity;
					$outdiscount=$objp->remise_percent;
					if ($objp->quantity == 1)
					{
						$opt.= price($objp->fprice,1,$langs,0,0,-1,$conf->currency)."/";
						$outval.= price($objp->fprice,0,$langs,0,0,-1,$conf->currency)."/";
						$opt.= $langs->trans("Unit");   // Do not use strtolower because it breaks utf8 encoding
						$outval.=$langs->transnoentities("Unit");
					}
					else
					{
						$opt.= price($objp->fprice,1,$langs,0,0,-1,$conf->currency)."/".$objp->quantity;
						$outval.= price($objp->fprice,0,$langs,0,0,-1,$conf->currency)."/".$objp->quantity;
						$opt.= ' '.$langs->trans("Units");  // Do not use strtolower because it breaks utf8 encoding
						$outval.= ' '.$langs->transnoentities("Units");
					}

					if ($objp->quantity >= 1)
					{
						$opt.=" (".price($objp->unitprice,1,$langs,0,0,-1,$conf->currency)."/".$langs->trans("Unit").")";   // Do not use strtolower because it breaks utf8 encoding
						$outval.=" (".price($objp->unitprice,0,$langs,0,0,-1,$conf->currency)."/".$langs->transnoentities("Unit").")";  // Do not use strtolower because it breaks utf8 encoding
					}
					if ($objp->remise_percent >= 1)
					{
						$opt.=" - ".$langs->trans("Discount")." : ".vatrate($objp->remise_percent).' %';
						$outval.=" - ".$langs->transnoentities("Discount")." : ".vatrate($objp->remise_percent).' %';
					}
					if ($objp->duration)
					{
						$opt .= " - ".$objp->duration;
						$outval.=" - ".$objp->duration;
					}
					if (! $socid)
					{
						$opt .= " - ".dol_trunc($objp->name,8);
						$outval.=" - ".dol_trunc($objp->name,8);
					}
				}
				else
				{
					$opt.= $langs->trans("NoPriceDefinedForThisSupplier");
					$outval.=$langs->transnoentities("NoPriceDefinedForThisSupplier");
				}
				$opt .= "</option>\n";


				// Add new entry
				// "key" value of json key array is used by jQuery automatically as selected value
				// "label" value of json key array is used by jQuery automatically as text for combo box
				$out.=$opt;
				array_push($outarray, array('key'=>$outkey, 'value'=>$outref, 'label'=>$outval, 'qty'=>$outqty, 'discount'=>$outdiscount, 'disabled'=>(empty($objp->idprodfournprice)?true:false)));
				// Exemple of var_dump $outarray
				// array(1) {[0]=>array(6) {[key"]=>string(1) "2" ["value"]=>string(3) "ppp"
				//           ["label"]=>string(76) "ppp (<strong>f</strong>ff2) - ppp - 20,00 Euros/1unité (20,00 Euros/unité)"
				//           ["qty"]=>string(1) "1" ["discount"]=>string(1) "0" ["disabled"]=>bool(false)
				//}
				//var_dump($outval); var_dump(utf8_check($outval)); var_dump(json_encode($outval));
				//$outval=array('label'=>'ppp (<strong>f</strong>ff2) - ppp - 20,00 Euros/ Unité (20,00 Euros/unité)');
				//var_dump($outval); var_dump(utf8_check($outval)); var_dump(json_encode($outval));

				$i++;
			}
			$out.='</select>';

			$this->db->free($result);

			if (empty($outputmode)) return $out;
			return $outarray;
		}
		else
		{
			dol_print_error($this->db);
		}
	}

	/**
	 *  Return list of suppliers prices for a product
	 *
	 *  @param      int     $productid       Id of product
	 *  @param      string  $htmlname        Name of HTML field
	 *  @return     void
	 */
	function select_product_fourn_price_c($productid,$htmlname='productfournpriceid')
	{
		global $langs,$conf;

		$langs->load('stocks');

		$sql = "SELECT p.rowid, p.label, p.ref, p.price, p.duration,";
		$sql.= " pfp.ref_fourn, pfp.rowid as idprodfournprice, pfp.price as fprice, pfp.quantity, pfp.unitprice,";
		$sql.= " s.nom as name";
		$sql.= " FROM ".MAIN_DB_PREFIX."product as p";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product_fournisseur_price as pfp ON p.rowid = pfp.fk_product";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON pfp.fk_soc = s.rowid";
		$sql.= " WHERE p.entity IN (".getEntity('product', 1).")";
		$sql.= " AND p.tobuy = 1";
		$sql.= " AND s.fournisseur = 1";
		$sql.= " AND p.rowid = ".$productid;
		$sql.= " ORDER BY s.nom, pfp.ref_fourn DESC";

		dol_syslog(get_class($this)."::select_product_fourn_price", LOG_DEBUG);
		$result=$this->db->query($sql);

		if ($result)
		{
			$num = $this->db->num_rows($result);

			$form = '<select class="flat" name="'.$htmlname.'">';

			if (! $num)
			{
				$form.= '<option value="0">-- '.$langs->trans("NoSupplierPriceDefinedForThisProduct").' --</option>';
			}
			else
			{
				$form.= '<option value="0">&nbsp;</option>';

				$i = 0;
				while ($i < $num)
				{
					$objp = $this->db->fetch_object($result);

					$opt = '<option value="'.$objp->idprodfournprice.'"';
					//if there is only one supplier, preselect it
					if($num == 1) {
						$opt .= ' selected="selected"';
					}
					$opt.= '>'.$objp->name.' - '.$objp->ref_fourn.' - ';

					if ($objp->quantity == 1)
					{
						$opt.= price($objp->fprice,1,$langs,0,0,-1,$conf->currency)."/";
					}

					$opt.= $objp->quantity.' ';

					if ($objp->quantity == 1)
					{
						$opt.= $langs->trans("Unit");
					}
					else
					{
						$opt.= $langs->trans("Units");
					}
					if ($objp->quantity > 1)
					{
						$opt.=" - ";
						$opt.= price($objp->unitprice,1,$langs,0,0,-1,$conf->currency)."/".$langs->trans("Unit");
					}
					if ($objp->duration) $opt .= " - ".$objp->duration;
					$opt .= "</option>\n";

					$form.= $opt;
					$i++;
				}
				$form.= '</select>';

				$this->db->free($result);
			}
			return $form;
		}
		else
		{
			dol_print_error($this->db);
		}
	}

	function load_type_purchase($htmlname='type_purchase', $selected='', $showempty=0,$campo='rowid', $options_only=false)
	{
		global $langs,$conf,$mysoc;

		$return='';
		$txid=array();
		$txcode=array();
		$txlabel=array();

		// Now we get list
		$num = $this->load_cache_typepurchase();

		if ($num > 0)
		{
			$defaulttx = $selected;
			// Disabled if seller is not subject to VAT
			$disabled=false; $title='';

			if (! $options_only) $return.= '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'"'.($disabled?' disabled="disabled"':'').$title.'>';

			if ($showempty) $return.= '<option value=-1>'.$langs->trans('Select').'</option>';
			foreach ($this->cache_type_purchase as $rate)
			{
				$lView = true;
				if ($lView)
				{
					$return.= '<option value="'.$rate[$campo];
					$return.= '"';
					if ($rate[$campo] == $defaulttx)
					{
						$return.= ' selected="selected"';
					}
					$return.= '>'.$rate['code'];
					$return.= $rate['label'] ? ' '.$rate['label']: '';
					
					$return.= '</option>';

					$this->type_purchase_id[]    = $rate['rowid'];
					$this->type_purchase_code[]  = $rate['code'];
					$this->type_purchase_label[] = $rate['label'];
				}
			}

			if (! $options_only) $return.= '</select>';
		}
		else
		{
			$return.= $this->error;
		}

		$this->num = $num;
		return $return;
	}


	/**
	 *  Load into the cache type facture
	 *
	 *  @return int                         Nb of loaded lines, 0 if already loaded, <0 if KO
	 */
	function load_cache_typepurchase()
	{
		global $langs;

		$num = count($this->cache_type_tva);
		if ($num > 0) return $num;    
		// Cache deja charge

		$sql  = "SELECT DISTINCT t.rowid, t.code, t.label";
		$sql.= " FROM ".MAIN_DB_PREFIX."c_type_purchase as t ";
		$sql.= " WHERE t.active = 1";
		$sql.= " ORDER BY t.label ASC";

		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				for ($i = 0; $i < $num; $i++)
				{
					$obj = $this->db->fetch_object($resql);
					$this->cache_type_purchase[$i]['rowid']  = $obj->rowid;
					$this->cache_type_purchase[$i]['code'] = $obj->code;
					$this->cache_type_purchase[$i]['label'] = $obj->label;
				}
				return $num;
			}
			else
			{
				$this->error = '<font class="error">'.$langs->trans("ErrorNoTypePurchaseDefined",$country_code).'</font>';
				return -1;
			}
		}
		else
		{
			$this->error = '<font class="error">'.$this->db->error().'</font>';
			return -2;
		}
	}

	/**
	 *  Show linked object block.
	 *
	 *  @param  CommonObject    $object     Object we want to show links to
	 *  @return int                         <0 if KO, >0 if OK
	 */
	function showLinkedObjectBlockpurchase($object)
	{
		global $conf,$langs,$hookmanager;
		global $bc;

		$object->fetchObjectLinked();

		// Bypass the default method
		$hookmanager->initHooks(array('commonobject'));
		$parameters=array();
		$reshook=$hookmanager->executeHooks('showLinkedObjectBlock',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook

		if (empty($reshook))
		{
			$num = count($object->linkedObjects);
			$numoutput=0;

			foreach($object->linkedObjects as $objecttype => $objects)
			{
				$tplpath = $element = $subelement = $objecttype;

				if ($objecttype != 'supplier_proposal' && preg_match('/^([^_]+)_([^_]+)/i',$objecttype,$regs))
				{
					$element = $regs[1];
					$subelement = $regs[2];
					$tplpath = $element.'/'.$subelement;
				}
				$tplname='linkedobjectblock';
				
				// To work with non standard path
				if ($objecttype == 'facture')          {
					$tplpath = 'compta/'.$element;
					if (empty($conf->facture->enabled)) continue;   // Do not show if module disabled
				}
				else if ($objecttype == 'facturerec')          {
					$tplpath = 'compta/facture';
					$tplname = 'linkedobjectblockForRec';
					if (empty($conf->facture->enabled)) continue;   // Do not show if module disabled
				}
				else if ($objecttype == 'propal')           {
					$tplpath = 'comm/'.$element;
					if (empty($conf->propal->enabled)) continue;    // Do not show if module disabled
				}
				else if ($objecttype == 'supplier_proposal')           {
					if (empty($conf->supplier_proposal->enabled)) continue; // Do not show if module disabled
				}
				else if ($objecttype == 'shipping' || $objecttype == 'shipment') {
					$tplpath = 'expedition';
					if (empty($conf->expedition->enabled)) continue;    // Do not show if module disabled
				}
				else if ($objecttype == 'delivery')         {
					$tplpath = 'livraison';
					if (empty($conf->expedition->enabled)) continue;    // Do not show if module disabled
				}
				else if ($objecttype == 'invoice_supplier') {
					$tplpath = 'purchase/facture';
				}
				else if ($objecttype == 'order_supplier')   {
					$tplpath = 'purchase/commande';
				}
				else if ($objecttype == 'expensereport')   {
					$tplpath = 'expensereport';
				}
				else if ($objecttype == 'subscription')   {
					$tplpath = 'adherents';
				}
				
				global $linkedObjectBlock;
				$linkedObjectBlock = $objects;

				if (empty($numoutput))
				{
					$numoutput++;
					
					print '<br>';
					print load_fiche_titre($langs->trans('RelatedObjects'), '', '');
					
					print '<table class="noborder allwidth">';

					print '<tr class="liste_titre">';
					print '<td>'.$langs->trans("Type").'</td>';
					print '<td>'.$langs->trans("Ref").'</td>';
					print '<td align="center"></td>';
					print '<td align="center">'.$langs->trans("Date").'</td>';
					print '<td align="right">'.$langs->trans("AmountTTCShort").'</td>';
					print '<td align="right">'.$langs->trans("Status").'</td>';
					print '<td></td>';
					print '</tr>';
				}

				// Output template part (modules that overwrite templates must declare this into descriptor)
				$dirtpls=array_merge($conf->modules_parts['tpl'],array('/'.$tplpath.'/tpl'));
				foreach($dirtpls as $reldir)
				{
					$res=@include dol_buildpath($reldir.'/'.$tplname.'.tpl.php');
					if ($res) break;
				}
			}

			if ($numoutput) 
			{
				print '</table>';
			}

			return $num;
		}
	}    

	/**
	 *  Return list of products for customer in Ajax if Ajax activated or go to select_produits_list
	 *
	 *  @param      int         $selected               Preselected products
	 *  @param      string      $htmlname               Name of HTML select field (must be unique in page)
	 *  @param      int         $filtertype             Filter on product type (''=nofilter, 0=product, 1=service)
	 *  @param      int         $limit                  Limit on number of returned lines
	 *  @param      int         $price_level            Level of price to show
	 *  @param      int         $status                 -1=Return all products, 0=Products not on sell, 1=Products on sell
	 *  @param      int         $finished               2=all, 1=finished, 0=raw material
	 *  @param      string      $selected_input_value   Value of preselected input text (with ajax)
	 *  @param      int         $hidelabel              Hide label (0=no, 1=yes, 2=show search icon (before) and placeholder, 3 search icon after)
	 *  @param      array       $ajaxoptions            Options for ajax_autocompleter
	 *  @param      int         $socid                  Thirdparty Id
	 *  @return     void
	 */
	function select_company_v($selected='', $htmlname='socid', $filtertype='', $limit=20, $price_level=0, $status=1, $finished=2, $selected_input_value='', $hidelabel=0, $ajaxoptions=array(),$socid=0,$action='',$filterstatic='')
	{
		global $langs,$conf;
		//include_once(DOL_DOCUMENT_ROOT.'/purchase/tpl/frames.tpl.php');
		$price_level = (! empty($price_level) ? $price_level : 0);

		if (! empty($conf->use_javascript_ajax) && ! empty($conf->global->COMPANY_USE_SEARCH_TO_SELECT))
		{
			$placeholder='';
			if ($selected && empty($selected_input_value))
			{
				require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
				$societe = new Societe($this->db);
				$societe->fetch($selected);
				$selected_input_value=$societe->ref;
			}
			// mode=1 means customers products
			$urloption='htmlname='.$htmlname.'&outjson=1&price_level='.$price_level.'&type='.$filtertype.'&mode=1&status='.$status.'&finished='.$finished;
			print ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/purchase/ajax/societes.php', $urloption, $conf->global->COMPANY_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
			if (empty($hidelabel)) print $langs->trans("RefOrLabel").' : ';
			else if ($hidelabel > 1) {
				if (! empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $placeholder=' placeholder="'.$langs->trans("RefOrLabel").'"';
				else $placeholder=' title="'.$langs->trans("RefOrLabel").'"';
				if ($hidelabel == 2) {
					print img_picto($langs->trans("Search"), 'search');
				}
			}
			print '<input type="text" class="theight20" size="10" name="search_'.$htmlname.'" id="search_'.$htmlname.'" value="'.$selected_input_value.'"'.$placeholder.' '.'/>';

			if ($hidelabel == 3) {
				print img_picto($langs->trans("Search"), 'search');
			}
		}
		else
		{
			print $this->select_client_list_v($selected,$htmlname,$filtertype,$limit,$price_level,$filterkey,$status,$finished,0,$socid,$filterstatic);
		}
	}

	/**
	 *  Return list of products for a customer
	 *
	 *  @param      int     $selected       Preselected product
	 *  @param      string  $htmlname       Name of select html
	 *  @param      string  $filtertype     Filter on product type (''=nofilter, 0=product, 1=service)
	 *  @param      int     $limit          Limit on number of returned lines
	 *  @param      int     $price_level    Level of price to show
	 *  @param      string  $filterkey      Filter on product
	 *  @param      int     $status         -1=Return all products, 0=Products not on sell, 1=Products on sell
	 *  @param      int     $finished       Filter on finished field: 2=No filter
	 *  @param      int     $outputmode     0=HTML select string, 1=Array
	 *  @param      int     $socid          Thirdparty Id
	 *  @return     array                   Array of keys for json
	 */
	function select_client_list_v($selected='',$htmlname='socid',$filtertype='',$limit=20,$price_level=0,$filterkey='',$status=1,$finished=2,$outputmode=0,$socid=0,$filterstatic='')
	{
		global $langs,$conf,$user,$db;

		$out='';
		$outarray=array();

		$sql = 'SELECT s.rowid, s.nom as name, s.name_alias, s.entity, s.ref_ext, s.ref_int, s.address, s.datec as date_creation, s.prefix_comm';
		$sql .= ', s.status';
		$sql .= ', s.price_level';
		$sql .= ', s.tms as date_modification';
		$sql .= ', s.phone, s.fax, s.email, s.skype, s.url, s.zip, s.town, s.note_private, s.note_public';
		$sql .= ', s.model_pdf, s.client, s.fournisseur';
		$sql .= ', s.siren as idprof1, s.siret as idprof2, s.ape as idprof3, s.idprof4, s.idprof5, s.idprof6';
		$sql .= ', s.capital, s.tva_intra';
		$sql .= ', s.fk_typent as typent_id';
		$sql .= ', s.fk_effectif as effectif_id';
		$sql .= ', s.fk_forme_juridique as forme_juridique_code';
		$sql .= ', s.webservices_url, s.webservices_key';
		$sql .= ', s.code_client, s.code_fournisseur, s.code_compta, s.code_compta_fournisseur, s.parent';
		$sql .= ', s.barcode';
		$sql .= ', s.fk_departement, s.fk_pays as country_id, s.fk_stcomm, s.remise_client, s.mode_reglement, s.cond_reglement, s.tva_assuj';
		$sql .= ', s.mode_reglement_supplier, s.cond_reglement_supplier, s.localtax1_assuj, s.localtax1_value, s.localtax2_assuj, s.localtax2_value, s.fk_prospectlevel, s.default_lang, s.logo';
		$sql .= ', s.fk_shipping_method';
		$sql .= ', s.outstanding_limit, s.import_key, s.canvas, s.fk_incoterms, s.location_incoterms';
		$sql .= ', s.fk_multicurrency, s.multicurrency_code';

		//$sql = "SELECT ";
		//$sql.= " p.rowid, p.label, p.ref, p.description, p.fk_product_type, p.price, p.price_ttc, p.price_base_type, p.tva_tx, p.duration, p.stock";

		//Price by customer
		//if (! empty($conf->global->PRODUIT_CUSTOMER_PRICES) && !empty($socid)) {
		//	$sql.=' ,pcp.rowid as idprodcustprice, pcp.price as custprice, pcp.price_ttc as custprice_ttc,';
		//	$sql.=' pcp.price_base_type as custprice_base_type, pcp.tva_tx as custtva_tx';
		//}

		// Multilang : we add translation

		$sql.= " FROM ".MAIN_DB_PREFIX."societe as s";
		$sql.= ' WHERE s.entity IN ('.getEntity('societe', 1).')';

		// Add criteria on ref/label
		if ($filterkey != '')
		{
			$sql.=' AND (';
			$prefix=empty($conf->global->COMPANY_DONOTSEARCH_ANYWHERE)?'%':'';  // Can use index if PRODUCT_DONOTSEARCH_ANYWHERE is on
			// For natural search
			$scrit = explode(' ', $filterkey);
			$i=0;
			if (count($scrit) > 1) $sql.="(";
			foreach ($scrit as $crit)
			{
				if ($i > 0) $sql.=" AND ";
				$sql.="(s.ref_ext LIKE '".$prefix.$crit."%' OR s.nom LIKE '".$prefix.$crit."%' OR s.tva_intra LIKE '".$prefix.$crit."%'";
				//if (! empty($conf->global->MAIN_MULTILANGS)) $sql.=" OR pl.label LIKE '".$prefix.$crit."%'";
				$sql.=")";
				$i++;
			}
			if (count($scrit) > 1) $sql.=")";
			//if (! empty($conf->barcode->enabled)) $sql.= " OR p.barcode LIKE '".$prefix.$filterkey."%'";
			$sql.=')';
		}
		if ($filterstatic) $sql.= $filterstatic;
		if ($filtertype) $sql.= " AND ".$filtertype;
		$sql.= $db->order("s.nom");
		$sql.= $db->plimit($limit);

		// Build output string
		dol_syslog(get_class($this)."::select_client_list_v search client sql=".$sql, LOG_DEBUG);
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

				$this->constructClientListOption_v($objp, $opt, $optJson, $price_level, $selected);
					// Add new entry
					// "key" value of json key array is used by jQuery automatically as selected value
					// "label" value of json key array is used by jQuery automatically as text for combo box
				$out.=$opt;
				array_push($outarray, $optJson);

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
	 * constructProductListOption
	 *
	 * @param   resultset   $objp           Resultset of fetch
	 * @param   string      $opt            Option
	 * @param   string      $optJson        Option
	 * @param   int         $price_level    Price level
	 * @param   string      $selected       Preselected value
	 * @return  void
	 */
	private function constructClientListOption_v(&$objp, &$opt, &$optJson, $price_level, $selected)
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

		$label=$objp->name;
		if (! empty($objp->label_translated)) $label=$objp->label_translated;
		if (! empty($filterkey) && $filterkey != '') $label=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$label,1);

		$outkey=$objp->rowid;
		$outref=$objp->ref;
		$outlabel=$objp->nom;
		$outdesc=$objp->tva_intra;
		$outtype=$objp->fk_product_type;

		$opt = '<option value="'.$objp->rowid.'"';
		$opt.= ($objp->rowid == $selected)?' selected="selected"':'';
		//$opt.= (!empty($objp->price_by_qty_rowid) && $objp->price_by_qty_rowid > 0)?' pbq="'.$objp->price_by_qty_rowid.'"':'';
		//if (! empty($conf->stock->enabled) && $objp->fk_product_type == 0 && isset($objp->stock))
		//{
		//	if ($objp->stock > 0) $opt.= ' class="product_line_stock_ok"';
		//	else if ($objp->stock <= 0) $opt.= ' class="product_line_stock_too_low"';
		//}
		$opt.= '>';
		$opt.= $objp->tva_intra.' - '.dol_trunc($label,32);
		//.' - ';
		//$opt.= $objp->description.' - ';

		$objRef = $objp->tva_intra;
		if (! empty($filterkey) && $filterkey != '') $objRef=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$objRef,1);
		$outval.=$objRef.' - '.dol_trunc($label,32);
		//.' - ';
		//$outval.=$objDesc.' - ';

		$found=0;

		$opt.= "</option>\n";
		$optJson = array('key'=>$outkey, 'value'=>$outref, 'label'=>$outval, 'label2'=>$outlabel, 'desc'=>$outdesc, 'type'=>$outtype, 'price_ht'=>$outprice_ht, 'price_ttc'=>$outprice_ttc, 'pricebasetype'=>$outpricebasetype, 'tva_tx'=>$outtva_tx, 'qty'=>$outqty, 'discount'=>$outdiscount);
	}

}
?>