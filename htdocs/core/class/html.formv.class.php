<?php
//Ramiro Queso <ramiroques@gmail.com>
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';

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
	function select_produits_v($selected='', $htmlname='productid', $filtertype='', $limit=20, $price_level=0, $status=1, $finished=2, $selected_input_value='', $hidelabel=0, $ajaxoptions=array(),$socid=0,$action='',$element='product',$fk_entrepot=0,$stockcero=0)
	{
		global $langs,$conf;

		include_once(DOL_DOCUMENT_ROOT.'/core/tpl2/frames.tpl.php');

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
				$unit = '';
				$labelproduct = $product->label;
				if ($conf->global->PRODUCT_USE_UNIT)
					$unit = $product->getLabelOfUnit('short');
			}

			// mode=1 means customers products
			$urloption='htmlname='.$htmlname.'&outjson=1&price_level='.$price_level.'&type='.$filtertype.'&mode=1&status='.$status.'&finished='.$finished.'&fk_entrepot='.$fk_entrepot.'&stockcero='.$stockcero;
			//Price by customer
			if (! empty($conf->global->PRODUIT_CUSTOMER_PRICES) && !empty($socid)) {
				$urloption.='&socid='.$socid;
			}
			//se direcciona a un solo lugar
			print ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/product/ajax/productsv.php', $urloption, $conf->global->PRODUIT_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
			if (empty($hidelabel)) print $langs->trans("RefOrLabel").' : ';
				else if ($hidelabel > 1) {
					if (! empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $placeholder=' placeholder="'.$langs->trans("RefOrLabel").'"';
					else $placeholder=' title="'.$langs->trans("RefOrLabel").'"';
					if ($hidelabel == 2) {
						print img_picto($langs->trans("Search"), 'search');
					}
				}
				print '<input type="text" class="theight20" size="13" name="search_'.$htmlname.'" id="search_'.$htmlname.'" value="'.$selected_input_value.'"'.$placeholder.' onblur="revisaFrame(this.value);"  '.($_SESSION['rf'] != 1?($action=='addpay'?'':'autofocus="autofocus"'):'').'/>';



				if ($hidelabel == 3) {
					print img_picto($langs->trans("Search"), 'search');
				}
			}
			else
			{
				print $this->select_produits_list_v($selected,$htmlname,$filtertype,$limit,$price_level,'',$status,$finished,0,$socid,$fk_entrepot,$stockcero);
			}
		}

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
	function select_produits_budget($fk_budget,$selected='', $htmlname='productid', $filtertype='', $limit=20, $price_level=0, $status=1, $finished=2, $selected_input_value='', $hidelabel=0, $ajaxoptions=array(),$socid=0,$action='',$element='product')
	{
		global $langs,$conf,$user;

		//include_once(DOL_DOCUMENT_ROOT.'/budget/tpl/framesbudget.tpl.php');

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
			$urloption.='&fk_budget='.$fk_budget;
			//Price by customer
			if (! empty($conf->global->PRODUIT_CUSTOMER_PRICES) && !empty($socid)) {
				$urloption.='&socid='.$socid;
			}
			print ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/budget/ajax/productsbudget.php', $urloption, $conf->global->PRODUIT_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
			if (empty($hidelabel)) print $langs->trans("RefOrLabel").' : ';
				else if ($hidelabel > 1) {
					if (! empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $placeholder=' placeholder="'.$langs->trans("RefOrLabel").'"';
					else $placeholder=' title="'.$langs->trans("RefOrLabel").'"';
					if ($hidelabel == 2) {
						print img_picto($langs->trans("Search"), 'search');
					}
				}
				print '<input type="text" class="theight20" size="10" name="search_'.$htmlname.'" id="search_'.$htmlname.'" value="'.$selected_input_value.'"'.$placeholder.' onkeyup="javascript:this.value=this.value.toUpperCase();" onblur="CambiaURLFrameb(this.value,'.$fk_budget.');" '.($_SESSION['rf'] != 1?($action=='addpay'?'':'autofocus="autofocus"'):'').'/>';

				if ($hidelabel == 3) {
					print img_picto($langs->trans("Search"), 'search');
				}
			}
			else
			{
				print $this->select_produits_list_budget($fk_budget,$selected,$htmlname,$filtertype,$limit,$price_level,'',$status,$finished,0,$socid);
			}
		}

		function select_produits_vxxx($selected='', $htmlname='productid', $filtertype='', $limit=20, $price_level=0, $status=1, $finished=2, $selected_input_value='', $hidelabel=0, $ajaxoptions=array(),$socid=0,$action='',$filterstatic='')
		{
			global $langs,$conf,$user;
		//include_once(DOL_DOCUMENT_ROOT.'/almacen/tpl/frames.tpl.php');
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
				print ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/product/ajax/productsv.php', $urloption, $conf->global->PRODUIT_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
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
	function select_produits_list_v($selected='',$htmlname='productid',$filtertype='',$limit=20,$price_level=0,$filterkey='',$status=1,$finished=2,$outputmode=0,$socid=0,$fk_entrepot=0,$stockcero=0)
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
		if (empty($stockcero))
		{
			if ($fk_entrepot>0) $sql.= ", ps.reel";
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
		if (empty($stockcero))
		{
			if ($fk_entrepot>0)
				$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product_stock as ps ON ps.fk_product = p.rowid AND ps.fk_entrepot=". $fk_entrepot;
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
		//echo $sql;exit;
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

				$objp->fk_entrepot = $fk_entrepot;
				$objp->stockcero = $stockcero;

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
	function select_produits_list_budget($fk_budget,$selected='',$htmlname='productid',$filtertype='',$limit=20,$price_level=0,$filterkey='',$status=1,$finished=2,$outputmode=0,$socid=0,$filterstatic='')
	{
		global $langs,$conf,$user;
		$langs->load("budget@budget");
		require_once DOL_DOCUMENT_ROOT.'/budget/class/budget.class.php';
		$budget = new Budget($this->db);
		$budget->fetch($fk_budget);
		$out='';
		$outarray=array();
		$selected = STRTOUPPER($selected);

		//cargamos los productos del proyecto
		$sql = "SELECT ";
		$sql.= " p.rowid, p.fk_product, p.label, p.ref";
		$sql.= " FROM ".MAIN_DB_PREFIX."product_budget as p";
		$sql.= ' WHERE p.fk_budget = '.$fk_budget;
		// Add criteria on ref/label
		if ($filterkey != '')
		{
			$sql.=' AND (';
			$prefix=empty($conf->global->PRODUCT_DONOTSEARCH_ANYWHERE)?'%':'';
			// Can use index if PRODUCT_DONOTSEARCH_ANYWHERE is on
			// For natural search
			$scrit = explode(' ', $filterkey);
			$i=0;
			if (count($scrit) > 1) $sql.="(";
			foreach ($scrit as $crit)
			{
				if ($i > 0) $sql.=" AND ";
				$sql.="(p.ref LIKE '".$prefix.$crit."%' OR p.label LIKE '".$prefix.$crit."%'";
				$sql.=")";
				$i++;
			}
			if (count($scrit) > 1) $sql.=")";
			$sql.=')';
		}
		if ($status == 1)
			$sql.= " AND p.status = 1 ";
		if ($filterstatic) $sql.= $filterstatic;
		$sql.= $this->db->order("p.ref");
		$sql.= $this->db->plimit($limit);

		// Build output string
		dol_syslog(get_class($this)."::select_produits_list_budget search product sql=".$sql, LOG_DEBUG);
		$result=$this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			if (empty($out))
			{
				$out.='<select class="flat" name="'.$htmlname.'" id="'.$htmlname.'">';
				$out.='<option value="0" selected="selected">&nbsp;</option>';
			}
			$i = 0;
			while ($num && $i < $num)
			{
				$opt = '';
				$optJson = array();
				$objp = $this->db->fetch_object($result);
				$objp->budget = 1;
				$objp->refbudget = $budget->ref;
				$this->constructProductListOption_v($objp, $opt, $optJson, $price_level, $selected);
					// Add new entry
					// "key" value of json key array is used by jQuery automatically as selected value
					// "label" value of json key array is used by jQuery automatically as text for combo box
				$out.=$opt;
				array_push($outarray, $optJson);
				$i++;
			}

			//$out.='</select>';

			//$this->db->free($result);

			//if (empty($outputmode)) return $out;
			//return $outarray;
		}
		else
		{
			dol_print_error($db);
		}

		if ($user->rights->budget->budr->prod)
		{
		//LISTA PRODUCTOS DE LA BASE
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
				$prefix=empty($conf->global->PRODUCT_DONOTSEARCH_ANYWHERE)?'%':'';
			// Can use index if PRODUCT_DONOTSEARCH_ANYWHERE is on
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
			$sql.= $this->db->order("p.ref");
			$sql.= $this->db->plimit($limit);

		// Build output string
			dol_syslog(get_class($this)."::select_produits_list_budget search product sql=".$sql, LOG_DEBUG);
			$result=$this->db->query($sql);
			if ($result)
			{
				$num = $this->db->num_rows($result);
				if (empty($out))
				{
			//	$out.='<select class="flat" name="'.$htmlname.'" id="'.$htmlname.'">';
			//	$out.='<option value="0" selected="selected">&nbsp;</option>';
				}
				$i = 0;
				while ($num && $i < $num)
				{
					$opt = '';
					$optJson = array();
					$objp = $this->db->fetch_object($result);

					if (!empty($objp->price_by_qty) && $objp->price_by_qty == 1 && !empty($conf->global->PRODUIT_CUSTOMER_PRICES_BY_QTY))
					{
				// Price by quantity will return many prices for the same product
						$sql = "SELECT rowid, quantity, price, unitprice, remise_percent, remise";
						$sql.= " FROM ".MAIN_DB_PREFIX."product_price_by_qty";
						$sql.= " WHERE fk_product_price=".$objp->price_rowid;
						$sql.= " ORDER BY quantity ASC";

						dol_syslog(get_class($this)."::select_produits_list_budget search price by qty sql=".$sql);
						$result2 = $this->db->query($sql);
						if ($result2)
						{
							$nb_prices = $this->db->num_rows($result2);
							$j = 0;
							while ($nb_prices && $j < $nb_prices)
							{
								$objp2 = $this->db->fetch_object($result2);

								$objp->quantity = $objp2->quantity;
								$objp->price = $objp2->price;
								$objp->unitprice = $objp2->unitprice;
								$objp->remise_percent = $objp2->remise_percent;
								$objp->remise = $objp2->remise;
								$objp->price_by_qty_rowid = $objp2->rowid;
								$objp->fk_entrepot = $fk_entrepot;
								$objp->stockcero = $stockcero;

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

			//se borro de aqui el fin de select
			}
			else
			{
				dol_print_error($db);
			}
		}

		////////////////////////

		$out.='</select>';

		$this->db->free($result);

		if (empty($outputmode)) return $out;
		return $outarray;

	}

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
	function select_produits_projet($fk_budget,$selected='', $htmlname='productid', $filtertype='', $limit=20, $price_level=0, $status=1, $finished=2, $selected_input_value='', $hidelabel=0, $ajaxoptions=array(),$socid=0,$action='',$element='product',$fk_entrepot=0,$forcecombo=false,$filterstatic='')
	{
		global $langs,$conf;


		$price_level = (! empty($price_level) ? $price_level : 0);

		if (! empty($conf->use_javascript_ajax) && ! empty($conf->global->PRODUIT_USE_SEARCH_TO_SELECT) && !$forcecombo)
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
			$urloption.='&fk_budget='.$fk_budget;
			$urloption.='&fk_entrepot='.$fk_entrepot;
			$urloption.='&filterstatic='.$filterstatic;
			//Price by customer
			if (! empty($conf->global->PRODUIT_CUSTOMER_PRICES) && !empty($socid)) {
				$urloption.='&socid='.$socid;
			}
			print ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/monprojet/ajax/productsprojet.php', $urloption, $conf->global->PRODUIT_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
			if (empty($hidelabel)) print $langs->trans("RefOrLabel").' : ';
				else if ($hidelabel > 1) {
					if (! empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $placeholder=' placeholder="'.$langs->trans("RefOrLabel").'"';
					else $placeholder=' title="'.$langs->trans("RefOrLabel").'"';
					if ($hidelabel == 2) {
						print img_picto($langs->trans("Search"), 'search');
					}
				}
				print '<input type="text" class="theight20" size="10" name="search_'.$htmlname.'" id="search_'.$htmlname.'" value="'.$selected_input_value.'"'.$placeholder.' onkeyup="javascript:this.value=this.value.toUpperCase();" onblur="CambiaURLFramep(this.value,'.$fk_budget.');" '.($_SESSION['rf'] != 1?($action=='addpay'?'':'autofocus="autofocus"'):'').'/>';

				if ($hidelabel == 3) {
					print img_picto($langs->trans("Search"), 'search');
				}
			}
			else
			{
				print $this->select_produits_list_projet($fk_budget,$selected,$htmlname,$filtertype,$limit,$price_level,'',$status,$finished,0,$socid,'',$fk_entrepot);
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
	function select_produits_list_projet($fk_budget,$selected='',$htmlname='productid',$filtertype='',$limit=20,$price_level=0,$filterkey='',$status=1,$finished=2,$outputmode=0,$socid=0,$filterstatic='',$fk_entrepot=0)
	{
		global $langs,$conf,$user,$db;
		$langs->load("monprojet@monprojet");
		$out='';
		$outarray=array();
		$selected = STRTOUPPER($selected);



		//askdjfñlaskjdfñlaksdjfñlaskjdfñlsad
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
		if ($fk_entrepot>0)
			$sql.= ", ps.reel";
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
		if ($fk_entrepot>0)
		{
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product_stock as ps ON ps.fk_product = p.rowid AND ps.fk_entrepot=". $fk_entrepot;
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
		dol_syslog(get_class($this)."::select_produits_list_projet search product sql=".$sql, LOG_DEBUG);
		$result=$this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			if (empty($out))
			{
				$out.='<select class="flat" name="'.$htmlname.'" id="'.$htmlname.'">';
				$out.='<option value="0" selected="selected">&nbsp;</option>';
			}
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

					dol_syslog(get_class($this)."::select_produits_list_projet search price by qty sql=".$sql);
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

			//se borro de aqui el fin de select
		}
		else
		{
			dol_print_error($db);
		}

		/*
		//cargamos los productos del proyecto
		$sql = "SELECT ";
		$sql.= " p.rowid, p.fk_product, p.label, p.ref";
		$sql.= " FROM ".MAIN_DB_PREFIX."product_projet as p";
		$sql.= ' WHERE p.fk_projet = '.$fk_budget;
		// Add criteria on ref/label
		if ($filterkey != '')
		{
			$sql.=' AND (';
			$prefix=empty($conf->global->PRODUCT_DONOTSEARCH_ANYWHERE)?'%':'';
			// Can use index if PRODUCT_DONOTSEARCH_ANYWHERE is on
			// For natural search
			$scrit = explode(' ', $filterkey);
			$i=0;
			if (count($scrit) > 1) $sql.="(";
			foreach ($scrit as $crit)
			{
				if ($i > 0) $sql.=" AND ";
				$sql.="(p.ref LIKE '".$prefix.$crit."%' OR p.label LIKE '".$prefix.$crit."%'";
				$sql.=")";
				$i++;
			}
			if (count($scrit) > 1) $sql.=")";
			$sql.=')';
		}
		if ($filterstatic) $sql.= $filterstatic;
		$sql.= $db->order("p.ref");
		$sql.= $db->plimit($limit);

		// Build output string
		dol_syslog(get_class($this)."::select_produits_list_projet search product sql=".$sql, LOG_DEBUG);
		$result=$this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			if (empty($out))
			{
			//$out.='<select class="flat" name="'.$htmlname.'" id="'.$htmlname.'">';
			//$out.='<option value="0" selected="selected">&nbsp;</option>';
			}
			$i = 0;
			while ($num && $i < $num)
			{
				$opt = '';
				$optJson = array();
				$objp = $this->db->fetch_object($result);
				$objp->budget = 1;
				$this->constructProductListOption_v($objp, $opt, $optJson, $price_level, $selected);
					// Add new entry
					// "key" value of json key array is used by jQuery automatically as selected value
					// "label" value of json key array is used by jQuery automatically as text for combo box
				$out.=$opt;
				array_push($outarray, $optJson);
				$i++;
			}

			//$out.='</select>';

			//$this->db->free($result);

			//if (empty($outputmode)) return $out;
			//return $outarray;
		}
		else
		{
			dol_print_error($db);
		}
		*/
		$out.='</select>';

		$this->db->free($result);

		if (empty($outputmode)) return $out;
		return $outarray;

	}


	//select item
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
	function select_items_v($selected='', $htmlname='productid', $filtertype='', $limit=20, $price_level=0, $status=1, $finished=2, $selected_input_value='', $hidelabel=0, $ajaxoptions=array(),$socid=0,$action='',$filter='')
	{
		global $langs,$conf;
		//include_once(DOL_DOCUMENT_ROOT.'/budget/tpl/framesitem.tpl.php');
		$price_level = (! empty($price_level) ? $price_level : 0);

		if (! empty($conf->use_javascript_ajax) && ! empty($conf->global->PRODUIT_USE_SEARCH_TO_SELECT))
		{
			$placeholder='';

			if ($selected && empty($selected_input_value))
			{
				require_once DOL_DOCUMENT_ROOT.'/budget/class/items.class.php';
				$product = new Items($this->db);
				$product->fetch($selected);
				$selected_input_value=$product->ref;
			}
			// mode=1 means customers products
			$urloption='htmlname='.$htmlname.'&outjson=1&price_level='.$price_level.'&type='.$filtertype.'&mode=1&status='.$status.'&finished='.$finished.'&selected='.$selected.'&filter='.$filter;
			//Price by customer
			if (! empty($conf->global->PRODUIT_CUSTOMER_PRICES) && !empty($socid)) {
				$urloption.='&socid='.$socid;
			}
			print ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/budget/ajax/items.php', $urloption, $conf->global->PRICEUNITS_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
			if (empty($hidelabel)) print $langs->trans("RefOrLabel").' : ';
				else if ($hidelabel > 1) {
					if (! empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $placeholder=' placeholder="'.$langs->trans("RefOrLabel").'"';
					else $placeholder=' title="'.$langs->trans("RefOrLabel").'"';
					if ($hidelabel == 2) {
						print img_picto($langs->trans("Search"), 'search');
					}
				}
				print '<input type="text" class="theight20" size="10" name="search_'.$htmlname.'" id="search_'.$htmlname.'" value="'.$selected_input_value.'"'.$placeholder.' onblur="CambiaURLFramei(this.value);" '.($_SESSION['rf'] != 1?($action=='addpay'?'':'autofocus="autofocus"'):'').'/>';

				if ($hidelabel == 3) {
					print img_picto($langs->trans("Search"), 'search');
				}
			}
			else
			{
				print $this->select_items_list_v($selected,$htmlname,$filtertype,$limit,$price_level,'',$status,$finished,0,$socid,$filter);
			}
		}

		function select_items_vxxx($selected='', $htmlname='itemid', $filtertype='', $limit=20, $price_level=0, $status=1, $finished=2, $selected_input_value='', $hidelabel=0, $ajaxoptions=array(),$socid=0,$action='',$filterstatic='')
		{
			global $langs,$conf;
		//include_once(DOL_DOCUMENT_ROOT.'/budget/tpl/framesitem.tpl.php');
			$price_level = (! empty($price_level) ? $price_level : 0);

			if (! empty($conf->use_javascript_ajax) && ! empty($conf->global->ITEMS_USE_SEARCH_TO_SELECT))
			{
				$placeholder='';
				if ($selected && empty($selected_input_value))
				{
					require_once DOL_DOCUMENT_ROOT.'/budget/class/items.class.php';
					$product = new Items($this->db);
					$product->fetch($selected);
					$selected_input_value=$product->ref;
				}
				// mode=1 means customers products
				$urloption='htmlname='.$htmlname.'&outjson=1&price_level='.$price_level.'&type='.$filtertype.'&mode=1&status='.$status.'&finished='.$finished;
				print ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/budget/ajax/items.php', $urloption, $conf->global->ITEMS_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
				if (empty($hidelabel)) print $langs->trans("RefOrLabel").' : ';
					else if ($hidelabel > 1) {
						if (! empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $placeholder=' placeholder="'.$langs->trans("RefOrLabel").'"';
						else $placeholder=' title="'.$langs->trans("RefOrLabel").'"';
						if ($hidelabel == 2) {
							print img_picto($langs->trans("Search"), 'search');
						}
					}
					print '<input type="text" class="theight20" size="10" name="search_'.$htmlname.'" id="search_'.$htmlname.'" value="'.$selected_input_value.'"'.$placeholder.' onblur="CambiaURLFramei(this.value);" '.'autofocus="autofocus"'.'/>';

					if ($hidelabel == 3) {
						print img_picto($langs->trans("Search"), 'search');
					}
				}
				else
				{
					print $this->select_items_list_v($selected,$htmlname,$filtertype,$limit,$price_level,$filterkey,$status,$finished,0,$socid,$filterstatic);
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
	 *  @param      int		$socid     		Thirdparty Id (to get also price dedicated to this customer)
	 *  @return     array    				Array of keys for json
	 */
	function select_items_list_v($selected='',$htmlname='itemid',$filtertype='',$limit=20,$price_level=0,$filterkey='',$status=1,$finished=2,$outputmode=0,$socid=0,$filter='')
	{
		global $langs,$conf,$user,$db;

		$out='';
		$outarray=array();

		$sql = "SELECT ";
		$sql.= " p.rowid, p.detail, p.detail AS label, p.ref, p.amount";

		$sql.= " FROM ".MAIN_DB_PREFIX."items as p";
		$sql.= ' WHERE p.entity IN ('.getEntity('items', 1).')';

		// Add criteria on ref/label
		if ($filterkey != '')
		{
			$sql.=' AND (';
			$prefix=empty($conf->global->PRICEUNITS_DONOTSEARCH_ANYWHERE)?'%':'';	// Can use index if PRODUCT_DONOTSEARCH_ANYWHERE is on
			// For natural search
			$scrit = explode(' ', $filterkey);
			$i=0;
			if (count($scrit) > 1) $sql.="(";
			foreach ($scrit as $crit)
			{
				if ($i > 0) $sql.=" AND ";
				$sql.="(p.ref LIKE '".$db->escape($prefix.$crit)."%' OR p.detail LIKE '".$db->escape($prefix.$crit)."%'";
				$sql.=")";
				$i++;
			}
			if (count($scrit) > 1) $sql.=")";
			$sql.=')';
		}
		$sql.= $db->order("p.ref");
		$sql.= $db->plimit($limit);

		// Build output string
		dol_syslog(get_class($this)."::select_items_list_v search items", LOG_DEBUG);
		$result=$this->db->query($sql);
		if ($result)
		{
			require_once DOL_DOCUMENT_ROOT.'/budget/class/items.class.php';
			$num = $this->db->num_rows($result);

			$out.='<select class="flat" name="'.$htmlname.'" id="'.$htmlname.'">';
			$out.='<option value="0" selected>&nbsp;</option>';

			$i = 0;
			while ($num && $i < $num)
			{
				$opt = '';
				$optJson = array();
				$objp = $this->db->fetch_object($result);


				$this->constructItemListOption($objp, $opt, $optJson, $price_level, $selected);
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
	 * @param 	resultset	$objp			    Resultset of fetch
	 * @param 	string		$opt			    Option (var used for returned value in string option format)
	 * @param 	string		$optJson		    Option (var used for returned value in json format)
	 * @param 	int			$price_level	    Price level
	 * @param 	string		$selected		    Preselected value
	 * @param   int         $hidepriceinlabel   Hide price in label
	 * @return	void
	 */
	private function constructItemListOption(&$objp, &$opt, &$optJson, $price_level, $selected, $hidepriceinlabel=0)
	{
		global $langs,$conf,$user,$db;

		$outkey='';
		$outval='';
		$outref='';
		$outlabel='';
		$outdesc='';
		$outbarcode='';
		$outtype='';
		$outprice_ht='';
		$outprice_ttc='';
		$outpricebasetype='';
		$outtva_tx='';
		$outqty=1;
		$outdiscount=0;

		$maxlengtharticle=(empty($conf->global->PRICEUNITS_MAX_LENGTH_COMBO)?48:$conf->global->PRICEUNITS_MAX_LENGTH_COMBO);

		$label=$objp->label;
		if (! empty($objp->label_translated)) $label=$objp->label_translated;
		if (! empty($filterkey) && $filterkey != '') $label=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$label,1);

		$outkey=$objp->rowid;
		$outref=$objp->ref;
		$outlabel=$objp->label;
		$outdesc=$objp->description;
		$outbarcode=$objp->barcode;

		$outtype=$objp->fk_product_type;
		//$outdurationvalue=$outtype == Product::TYPE_SERVICE?substr($objp->duration,0,dol_strlen($objp->duration)-1):'';
		//$outdurationunit=$outtype == Product::TYPE_SERVICE?substr($objp->duration,-1):'';

		$opt = '<option value="'.$objp->rowid.'"';
		$opt.= ($objp->rowid == $selected)?' selected':'';
		//$opt.= (!empty($objp->price_by_qty_rowid) && $objp->price_by_qty_rowid > 0)?' pbq="'.$objp->price_by_qty_rowid.'"':'';
		if (! empty($conf->stock->enabled) && $objp->fk_product_type == 0 && isset($objp->stock))
		{
		//	if ($objp->stock > 0) $opt.= ' class="product_line_stock_ok"';
		//	else if ($objp->stock <= 0) $opt.= ' class="product_line_stock_too_low"';
		}
		$opt.= '>';
		$opt.= $objp->ref;
		if ($outbarcode) $opt.=' ('.$outbarcode.')';
		$opt.=' - '.dol_trunc($label,$maxlengtharticle).' - ';

		$objRef = $objp->ref;
		if (! empty($filterkey) && $filterkey != '') $objRef=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$objRef,1);
		$outval.=$objRef;
		if ($outbarcode) $outval.=' ('.$outbarcode.')';
		$outval.=' - '.dol_trunc($label,$maxlengtharticle).' - ';

		$found=0;



		$opt.= "</option>\n";
		$optJson = array('key'=>$outkey, 'value'=>$outref, 'label'=>$outval, 'label2'=>$outlabel, 'desc'=>$outdesc, 'type'=>$outtype, 'price_ht'=>$outprice_ht, 'price_ttc'=>$outprice_ttc, 'pricebasetype'=>$outpricebasetype, 'tva_tx'=>$outtva_tx, 'qty'=>$outqty, 'discount'=>$outdiscount, 'duration_value'=>$outdurationvalue, 'duration_unit'=>$outdurationunit);
	}

	function select_items_list_vxxx($selected='',$htmlname='itemid',$filtertype='',$limit=20,$price_level=0,$filterkey='',$status=1,$finished=2,$outputmode=0,$socid=0,$filterstatic='')
	{
		global $langs,$conf,$user,$db;

		$out='';
		$outarray=array();

		$sql = "SELECT ";
		$sql.= " p.rowid, p.ref, p.detail, p.fk_type_item, p.quant, p.amount ";

		$sql.= " FROM ".MAIN_DB_PREFIX."items as p";
		$sql.= ' WHERE p.entity IN ('.getEntity('items', 1).')';
		// Add criteria on ref/label
		if ($filterkey != '')
		{
			$sql.=' AND (';
			$prefix=empty($conf->global->ITEMS_DONOTSEARCH_ANYWHERE)?'%':'';	// Can use index if PRODUCT_DONOTSEARCH_ANYWHERE is on
			// For natural search
			$scrit = explode(' ', $filterkey);
			$i=0;
			if (count($scrit) > 1) $sql.="(";
			foreach ($scrit as $crit)
			{
				if ($i > 0) $sql.=" AND ";
				$sql.="(p.ref LIKE '".$prefix.$crit."%' OR p.detail LIKE '".$prefix.$crit."%' OR p.especification LIKE '".$prefix.$crit."%'";
				$sql.=")";
				$i++;
			}
			if (count($scrit) > 1) $sql.=")";
			$sql.=')';
		}
		if ($filterstatic) $sql.= $filterstatic;
		$sql.= $db->order("p.ref");
		$sql.= $db->plimit($limit);

		// Build output string
		dol_syslog(get_class($this)."::select_items_list_v search items sql=".$sql, LOG_DEBUG);
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

				$this->constructItemsListOption_v($objp, $opt, $optJson, $price_level, $selected);
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

	//fin select item

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

		$langs->load("almacen@almacen");
		$langs->load("budget");
		$langs->load("monprojet");
		$langs->load("product");
		$langs->load("others");

		require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
		$objTmp = new Product($db);

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
		$objDesc=$objp->description;
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
		//marcamos si biene de budget
		if ($objp->budget) $opt.= $objp->refbudget.' -> ';
		$opt.= $objp->ref.' - '.dol_trunc($label,32).' - ';
		$opt.= $objp->description;
		if ($objp->stockcero && $objp->fk_entrepot)
		{
			$objTmp->fetch($objp->rowid);
			$objTmp->load_stock();
			$objp->reel = $objTmp->stock_warehouse[$objp->fk_entrepot]->real;
		}
		if (!$objp->stockcero && !$objp->fk_entrepot)
		{
			$objTmp->fetch($objp->rowid);
			$objTmp->load_stock();
			foreach ($objTmp->stock_warehouse AS $j => $data)
			{
				$objp->reel+=$data->real;
			}
		}

		if ($objp->reel) $opt.= ' - '.$langs->trans('Balance').' '.price2num($objp->reel,'MU');
		else  $opt.= ' - '.$langs->trans('Balance').' 0';

		$objRef = $objp->ref;
		if (! empty($filterkey) && $filterkey != '') $objRef=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$objRef,1);
		if ($objp->budget) $outval.= $objp->refbudget.' -> ';
		$outval.=$objRef.' - '.dol_trunc($label,32).' - ';
		$outval.=$objDesc;
		if ($objp->reel) $outval.= ' - '.$langs->trans('Balance').' '.price2num($objp->reel,'MU');
		else  $outval.= ' - '.$langs->trans('Balance').' 0';

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
		if (!empty($conf->global->PRODUIT_CUSTOMER_PRICES))
		{
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
			//$opt.= '  - '.$langs->trans("Stock").':'.$objp->stock;
			//$outval.='  - '.$langs->transnoentities("Stock").':'.$objp->stock;
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
	 * constructProductListOption
	 *
	 * @param 	resultset	$objp			Resultset of fetch
	 * @param 	string		$opt			Option
	 * @param 	string		$optJson		Option
	 * @param 	int			$price_level	Price level
	 * @param 	string		$selected		Preselected value
	 * @return	void
	 */
	private function constructItemsListOption_v(&$objp, &$opt, &$optJson, $price_level, $selected)
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

		$label=$objp->detail;
		if (! empty($objp->label_translated)) $label=$objp->label_translated;
		if (! empty($filterkey) && $filterkey != '') $label=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$label,1);

		$outkey=$objp->rowid;
		$outref=$objp->ref;
		$outlabel=$objp->detail;
		$outdesc=$objp->description;
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
		$opt.= $objp->ref.' - '.dol_trunc($label,32).' - ';
		//$opt.= $objp->description.' - ';

		$objRef = $objp->ref;
		if (! empty($filterkey) && $filterkey != '') $objRef=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$objRef,1);
		$outval.=$objRef.' - '.dol_trunc($label,32).' - ';
		//$outval.=$objDesc.' - ';

		$found=1;
		// If level no defined or multiprice not found, we used the default price

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
			print '<input type="hidden" name="action" value="setdeliveryentrepot">';
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

	function select_currency_($selected='',$htmlname='currency_id')
	{
		print $this->selectcurrency_($selected,$htmlname);
	}

	function selectCurrency_($selected='',$htmlname='currency_id')
	{
		global $conf,$langs,$user;

		$langs->loadCacheCurrencies('');

		$out='';
		if ($selected=='euro' || $selected=='euros') $selected='EUR';
		  // Pour compatibilite

		$out.= '<select class="form-control flat" name="'.$htmlname.'" id="'.$htmlname.'">';
		foreach ($langs->cache_currencies as $code_iso => $currency)
		{
			if ($selected && $selected == $code_iso)
			{
				$out.= '<option value="'.$code_iso.'" selected>';
			}
			else
			{
				$out.= '<option value="'.$code_iso.'">';
			}
			$out.= $currency['label'];
			$out.= ' ('.$langs->getCurrencySymbol($code_iso).')';
			$out.= '</option>';
		}
		$out.= '</select>';
		if ($user->admin) $out.= info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
		return $out;
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
			//Price by customer
			print ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/societe/ajax/societes.php', $urloption, $conf->global->COMPANY_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
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

	/**
	 *  Output html form to select a members
	   *
	   *	@param	string	$selected       Preselected type
	   *	@param  string	$htmlname       Name of field in form
	   *  @param  string	$filter         optional filters criteras (example: 's.rowid <> x')
	   *	@param	int		$showempty		Add an empty field
	   * 	@param	int		$showtype		Show third party type in combolist (customer, prospect or supplier)
	   * 	@param	int		$forcecombo		Force to use combo box
	   *  @param	array	$events			Event options to run on change. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
	   *	@param	int		$limit			Maximum number of elements
	   * 	@return	string					HTML string with
	   *  @deprecated						Use select_thirdparty instead
	   */
	function select_member($selected='', $htmlname='fk_member', $filter='', $showempty=0, $showtype=0, $forcecombo=0, $events=array(), $limit=0)
	{
		return $this->select_member_list($selected, $htmlname, $filter, $showempty, $showtype, $forcecombo, $events, '', 0, $limit);
	}

	/**
	   *  Output html form to select a members
	   *
	   *	@param	string	$selected       Preselected type
	   *	@param  string	$htmlname       Name of field in form
	   *  @param  string	$filter         optional filters criteras (example: 's.rowid <> x')
	   *	@param	int		$showempty		Add an empty field
	   * 	@param	int		$showtype		Show third party type in combolist (customer, prospect or supplier)
	   * 	@param	int		$forcecombo		Force to use combo box
	   *  @param	array	$events			Event options. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
	   *  @param	string	$filterkey		Filter on key value
	   *  @param	int		$outputmode		0=HTML select string, 1=Array
	   *  @param	int		$limit			Limit number of answers
	   * 	@return	string					HTML string with
	   */
	function select_member_list($selected='',$htmlname='fk_member',$filter='',$showempty=0, $showtype=0, $forcecombo=0, $events=array(), $filterkey='', $outputmode=0, $limit=0)
	{
		global $conf,$user,$langs;

		$out=''; $num=0;
		$outarray=array();
		//sql
		$sql = "SELECT d.rowid, d.ref_ext, d.civility as civility_id, d.firstname, d.lastname, d.societe as company, d.fk_soc, d.statut, d.public, d.address, d.zip, d.town, ";
		$sql.= " d.email, d.skype, d.phone, d.phone_perso, d.phone_mobile, d.login, ";
		$sql.= " d.fk_adherent_type, d.entity,";
		$sql.= " d.datec as datec,";
		$sql.= " d.datefin as datefin,";
		$sql.= " d.birth as birthday,";
		$sql.= " d.country,";
		$sql.= " d.state_id,";
		$sql.= " c.rowid as country_id, c.code as country_code, c.label as country,";
		$sql.= " dep.nom as state, dep.code_departement as state_code,";
		$sql.= " t.libelle as type ";
		//$sql.= " , u.rowid as user_id, u.login as user_login";
		$sql.= " FROM ".MAIN_DB_PREFIX."adherent_type as t, ".MAIN_DB_PREFIX."adherent as d";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_country as c ON d.country = c.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_departements as dep ON d.state_id = dep.rowid";
		//$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."user as u ON d.rowid = u.fk_member";
		$sql.= " WHERE d.fk_adherent_type = t.rowid";
		if ($filter) $sql.= " AND (".$filter.")";
		$sql.= " AND d.entity IN (".getEntity().")";

		if (! empty($conf->global->MEMBER_HIDE_INACTIVE_IN_COMBOBOX)) $sql.= " AND d.statut<>0 ";
		if ($filterkey && $filterkey != '')
		{
			$sql.=" AND (";
			if (! empty($conf->global->MEMBER_DONOTSEARCH_ANYWHERE))
			{
				$sql.="(d.lastname LIKE '".$this->db->escape($filterkey)."%')";
			}
			else
			{
				// For natural search
				$scrit = explode(' ', $filterkey);
				foreach ($scrit as $crit)
				{
					$sql.=" AND (";
					$sql.=" d.lastname LIKE '%".$this->db->escape($crit)."%'";
					$sql.= " OR d.firstname LIKE '%".$this->db->escape($crit)."%'";
					$sql.= " OR d.login LIKE '%".$this->db->escape($crit)."%'";
					$sql.= " OR d.email LIKE '%".$this->db->escape($crit)."%'";
					$sql.= " OR d.skype LIKE '%".$this->db->escape($crit)."%'";
					$sql.= " OR d.phone LIKE '%".$this->db->escape($crit)."%'";
					$sql.= " OR d.phone_perso LIKE '%".$this->db->escape($crit)."%'";
					$sql.= " OR d.phone_mobile LIKE '%".$this->db->escape($crit)."%'";
					$sql.=")";
				}
			}
			$sql.=")";
		}
		$sql.=$this->db->order("d.lastname","ASC");
		if ($limit > 0) $sql.=$this->db->plimit($limit);

		dol_syslog(get_class($this)."::select_member_list", LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if (! empty($conf->use_javascript_ajax))
			{
				if (! empty($conf->global->MEMBER_USE_SEARCH_TO_SELECT) && ! $forcecombo)
				{
					include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
					$out.= ajax_combobox($htmlname, $events, $conf->global->MEMBER_USE_SEARCH_TO_SELECT);
				}
				else
				{
					if (count($events))
					{
						$out.='<script type="text/javascript">
						$(document).ready(function() {
							jQuery("#'.$htmlname.'").change(function () {
								var obj = '.json_encode($events).';
								$.each(obj, function(key,values) {
									if (values.method.length) {
										runJsCodeForEvent'.$htmlname.'(values);
									}
								});
							});

							function runJsCodeForEvent'.$htmlname.'(obj) {
								var id = $("#'.$htmlname.'").val();
								var method = obj.method;
								var url = obj.url;
								var htmlname = obj.htmlname;
								var showempty = obj.showempty;
								$.getJSON(url,
									{
										action: method,
										id: id,
										htmlname: htmlname,
										showempty: showempty
									},
									function(response) {
										$.each(obj.params, function(key,action) {
											if (key.length) {
												var num = response.num;
												if (num > 0) {
													$("#" + key).removeAttr(action);
												} else {
													$("#" + key).attr(action, action);
												}
											}
										});
										$("select#" + htmlname).html(response.value);
										if (response.num) {
											var selecthtml_str = response.value;
											var selecthtml_dom=$.parseHTML(selecthtml_str);
											$("#inputautocomplete"+htmlname).val(selecthtml_dom[0][0].innerHTML);
										} else {
											$("#inputautocomplete"+htmlname).val("");
										}
										$("select#" + htmlname).change();	/* Trigger event change */
									});
								} })
								</script>';
							}
						}
					}

			// Construct $out and $outarray
					$out.= '<select id="'.$htmlname.'" class="flat" name="'.$htmlname.'">'."\n";

					$textifempty='';
			// Do not use textempty = ' ' or '&nbsp;' here, or search on key will search on ' key'.
			//$textifempty=' ';
			//if (! empty($conf->use_javascript_ajax) || $forcecombo) $textifempty='';
					if ($showempty) $out.= '<option value="-1">'.$textifempty.'</option>'."\n";

					$num = $this->db->num_rows($resql);
					$i = 0;
					if ($num)
					{
						while ($i < $num)
						{
							$obj = $this->db->fetch_object($resql);
							$label='';
							$label=$obj->lastname.' '.$obj->firstname.' - '.$obj->login.': '.$langs->trans('Phone').': '.$obj->phone;

							if ($selected > 0 && $selected == $obj->rowid)
							{
								$out.= '<option value="'.$obj->rowid.'" selected="selected">'.$label.'</option>';
							}
							else
							{
								$out.= '<option value="'.$obj->rowid.'">'.$label.'</option>';
							}

							array_push($outarray, array('key'=>$obj->rowid, 'value'=>$obj->name, 'label'=>$obj->name));

							$i++;
							if (($i % 10) == 0) $out.="\n";
						}
					}
					$out.= '</select>'."\n";
				}
				else
				{
					dol_print_error($this->db);
				}

				$this->result=array('nbofmember'=>$num);

				if ($outputmode) return $outarray;
				return $out;
			}


	/**
   *  Output html form to select a user
   *
   *	@param	string	$selected       Preselected type
   *	@param  string	$htmlname       Name of field in form
   *  @param  string	$filter         optional filters criteras (example: 's.rowid <> x')
   *	@param	int		$showempty		Add an empty field
   * 	@param	int		$showtype		Show third party type in combolist (customer, prospect or supplier)
   * 	@param	int		$forcecombo		Force to use combo box
   *  @param	array	$events			Event options to run on change. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
   *	@param	int		$limit			Maximum number of elements
   * 	@return	string					HTML string with
   *  @deprecated						Use select_thirdparty instead
   */
	function select_use($selected='', $htmlname='userid', $filter='', $showempty=0, $showtype=0, $forcecombo=0, $events=array(), $limit=0,$required='')
	{
		return $this->select_use_list($selected, $htmlname, $filter, $showempty, $showtype, $forcecombo, $events, '', 0, $limit,$required);
	}

	/**
   *  Output html form to select a user
   *
   *	@param	string	$selected       Preselected type
   *	@param  string	$htmlname       Name of field in form
   *  @param  string	$filter         optional filters criteras (example: 's.rowid <> x')
   *	@param	int		$showempty		Add an empty field
   * 	@param	int		$showtype		Show third party type in combolist (customer, prospect or supplier)
   * 	@param	int		$forcecombo		Force to use combo box
   *  @param	array	$events			Event options. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
   *  @param	string	$filterkey		Filter on key value
   *  @param	int		$outputmode		0=HTML select string, 1=Array
   *  @param	int		$limit			Limit number of answers
   * 	@return	string					HTML string with
   */
	function select_use_list($selected='',$htmlname='userid',$filter='',$showempty=0, $showtype=0, $forcecombo=0, $events=array(), $filterkey='', $outputmode=0, $limit=0,$required='')
	{
		global $conf,$user,$langs;

		$out=''; $num=0;
		$outarray=array();
		//sql
		$sql = "SELECT u.rowid, u.lastname, u.firstname, u.email, u.job, u.skype, u.signature, u.office_phone, u.office_fax, u.user_mobile,";
		$sql.= " u.admin, u.login, u.note,";
		$sql.= " u.pass, u.pass_crypted, u.pass_temp,";
		//$sql.= " u.fk_societe,";
		$sql.= " u.fk_socpeople, u.fk_member, u.fk_user, u.ldap_sid,";
		$sql.= " u.statut, u.lang, u.entity,";
		$sql.= " u.datec as datec,";
		$sql.= " u.tms as datem,";
		$sql.= " u.datelastlogin as datel,";
		$sql.= " u.datepreviouslogin as datep,";
		$sql.= " u.photo as photo,";
		$sql.= " u.openid as openid,";
		$sql.= " u.accountancy_code,";
		$sql.= " u.thm,";
		$sql.= " u.ref_int, u.ref_ext";
		$sql.= " FROM ".MAIN_DB_PREFIX."user as u";
		if ((empty($conf->multicompany->enabled) || empty($conf->multicompany->transverse_mode)) && (! empty($user->entity)))
		{
			$sql.= " WHERE u.entity IN (0,".$conf->entity.")";
		}
		else
		{
			$sql.= " WHERE u.entity IS NOT NULL";
		}
		if ($filter) $sql.= " AND (".$filter.")";
		if (! empty($conf->global->USER_HIDE_INACTIVE_IN_COMBOBOX)) $sql.= " AND u.statut<>0 ";

		if ($filterkey && $filterkey != '')
		{
			$sql.=" AND (";
			if (! empty($conf->global->USER_DONOTSEARCH_ANYWHERE))
			{
				$sql.="(u.lastname LIKE '".$this->db->escape($filterkey)."%'";
				$sql.= " OR u.firstname LIKE '".$this->db->escape($filterkey)."%'";
				$sql.= " OR u.login LIKE '".$this->db->escape($filterkey)."%'";
				$sql.= " OR u.email LIKE '".$this->db->escape($filterkey)."%'";
				$sql.= " OR u.skype LIKE '".$this->db->escape($filterkey)."%'";
				$sql.= " OR u.office_phone LIKE '".$this->db->escape($filterkey)."%'";
				$sql.= " OR u.user_mobile LIKE '".$this->db->escape($filterkey)."%'";
				$sql.=")";

			}
			else
			{
				// For natural search
				$scrit = explode(' ', $filterkey);
				foreach ($scrit as $crit)
				{
					$sql.=" AND (";
					$sql.=" u.lastname LIKE '%".$this->db->escape($crit)."%'";
					$sql.= " OR u.firstname LIKE '%".$this->db->escape($crit)."%'";
					$sql.= " OR u.login LIKE '%".$this->db->escape($crit)."%'";
					$sql.= " OR u.email LIKE '%".$this->db->escape($crit)."%'";
					$sql.= " OR u.skype LIKE '%".$this->db->escape($crit)."%'";
					$sql.= " OR u.office_phone LIKE '%".$this->db->escape($crit)."%'";
					$sql.= " OR u.user_mobile LIKE '%".$this->db->escape($crit)."%'";
					$sql.=")";
				}
			}
			$sql.=")";
		}
		$sql.=$this->db->order("u.lastname","ASC");
		if ($limit > 0) $sql.=$this->db->plimit($limit);

		dol_syslog(get_class($this)."::select_use_list", LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if (! empty($conf->use_javascript_ajax))
			{
				if (! empty($conf->global->USER_USE_SEARCH_TO_SELECT) && ! $forcecombo)
				{
					include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
					$out.= ajax_combobox($htmlname, $events, $conf->global->USER_USE_SEARCH_TO_SELECT);
				}
				else
				{
					if (count($events))
					{
						$out.='<script type="text/javascript">
						$(document).ready(function() {
							jQuery("#'.$htmlname.'").change(function () {
								var obj = '.json_encode($events).';
								$.each(obj, function(key,values) {
									if (values.method.length) {
										runJsCodeForEvent'.$htmlname.'(values);
									}
								});
							});

							function runJsCodeForEvent'.$htmlname.'(obj) {
								var id = $("#'.$htmlname.'").val();
								var method = obj.method;
								var url = obj.url;
								var htmlname = obj.htmlname;
								var showempty = obj.showempty;
								$.getJSON(url,
									{
										action: method,
										id: id,
										htmlname: htmlname,
										showempty: showempty
									},
									function(response) {
										$.each(obj.params, function(key,action) {
											if (key.length) {
												var num = response.num;
												if (num > 0) {
													$("#" + key).removeAttr(action);
												} else {
													$("#" + key).attr(action, action);
												}
											}
										});
										$("select#" + htmlname).html(response.value);
										if (response.num) {
											var selecthtml_str = response.value;
											var selecthtml_dom=$.parseHTML(selecthtml_str);
											$("#inputautocomplete"+htmlname).val(selecthtml_dom[0][0].innerHTML);
										} else {
											$("#inputautocomplete"+htmlname).val("");
										}
										$("select#" + htmlname).change();	/* Trigger event change */
									});
								} })
								</script>';
							}
						}
					}

			// Construct $out and $outarray
					$out.= '<select id="'.$htmlname.'" class="flat" name="'.$htmlname.'" '.$required.'>'."\n";

					$textifempty='';
			// Do not use textempty = ' ' or '&nbsp;' here, or search on key will search on ' key'.
			//$textifempty=' ';
			//if (! empty($conf->use_javascript_ajax) || $forcecombo) $textifempty='';
					if ($showempty) $out.= '<option value="-1">'.$textifempty.'</option>'."\n";

					$num = $this->db->num_rows($resql);
					$i = 0;
					if ($num)
					{
						while ($i < $num)
						{
							$obj = $this->db->fetch_object($resql);
							$label='';
							$label=$obj->lastname.' '.$obj->firstname;

							if ($selected > 0 && $selected == $obj->rowid)
							{
								$out.= '<option value="'.$obj->rowid.'" selected="selected">'.$label.'</option>';
							}
							else
							{
								$out.= '<option value="'.$obj->rowid.'">'.$label.'</option>';
							}

							array_push($outarray, array('key'=>$obj->rowid, 'value'=>$obj->lastname.' '.$obj->firstname, 'label'=>$obj->lastname.' '.$obj->firstname));

							$i++;
							if (($i % 10) == 0) $out.="\n";
						}
					}
					$out.= '</select>'."\n";
				}
				else
				{
					dol_print_error($this->db);
				}

				$this->result=array('nbofuser'=>$num);
				if ($outputmode)
				{
					return $outarray;
				}

				return $out;
			}

	/**
	 *  Output html form to select a third party
	 *
	 *	@param	string	$selected       Preselected type
	 *	@param  string	$htmlname       Name of field in form
	 *  @param  string	$filter         optional filters criteras (example: 's.rowid <> x')
	 *	@param	int		$showempty		Add an empty field
	 * 	@param	int		$showtype		Show third party type in combolist (customer, prospect or supplier)
	 * 	@param	int		$forcecombo		Force to use combo box
	 *  @param	array	$events			Event options. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
	 *  @param	string	$filterkey		Filter on key value
	 *  @param	int		$outputmode		0=HTML select string, 1=Array
	 *  @param	int		$limit			Limit number of answers
	 * 	@return	string					HTML string with
	 */
	function select_client_list($selected='',$htmlname='socid',$filter='',$showempty=0, $showtype=0, $forcecombo=0, $events=array(), $filterkey='', $outputmode=0, $limit=0)
	{
		global $conf,$user,$langs;

		$out=''; $num=0;
		$outarray=array();
		$sql = "SELECT s.rowid, s.nom, s.client, s.fournisseur, s.code_client, s.code_fournisseur, s.tva_intra ";
		$sql.= " FROM ".MAIN_DB_PREFIX ."societe as s";
		if (!$user->rights->ventas->cli->crear)
		{
			if (!$user->rights->societe->client->voir && !$user->societe_id)
				$sql .= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
		}
		$sql.= " WHERE s.entity IN (".getEntity('societe', 1).")";
		if (! empty($user->societe_id)) $sql.= " AND s.rowid = ".$user->societe_id;
		if ($filter) $sql.= " AND (".$filter.")";
		if (!$user->rights->ventas->cli->crear)
		{
			if (!$user->rights->societe->client->voir && !$user->societe_id)
				$sql.= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$user->id;
		}
		if (! empty($conf->global->COMPANY_HIDE_INACTIVE_IN_COMBOBOX)) $sql.= " AND s.status<>0 ";

		if ($filterkey && $filterkey != '')
		{
			$sql.=" AND (";
			if (! empty($conf->global->COMPANY_DONOTSEARCH_ANYWHERE))
			{
				$sql.="(s.name LIKE '".$this->db->escape($filterkey)."%')";
				$sql.=" OR s.tva_intra LIKE '".$this->db->escape($filterkey)."%'";
			}
			else
			{
				// For natural search
				$scrit = explode(' ', $filterkey);
				foreach ($scrit as $crit) {
					$sql.=" AND (s.name LIKE '%".$this->db->escape($crit)."%')";
					$sql.=" OR s.tva_intra LIKE '%".$this->db->escape($crit)."%'";
				}
			}
			if (! empty($conf->barcode->enabled))
			{
				$sql .= " OR s.barcode LIKE '".$this->db->escape($filterkey)."%'";
			}
			$sql.=")";
		}
		$sql.=$this->db->order("nom","ASC");
		if ($limit > 0) $sql.=$this->db->plimit($limit);

		dol_syslog(get_class($this)."::select_thirdparty_list sql=".$sql);

		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($conf->use_javascript_ajax && $conf->global->COMPANY_USE_SEARCH_TO_SELECT && ! $forcecombo)
			{
				include_once DOL_DOCUMENT_ROOT . '/core/lib/ajax.lib.php';
				$out.= ajax_combobox($htmlname, $events, $conf->global->COMPANY_USE_SEARCH_TO_SELECT);
			}

			// Construct $out and $outarray
			$out.= '<select id="'.$htmlname.'" class="flat" name="'.$htmlname.'">'."\n";
			if ($showempty) $out.= '<option value="-1"></option>'."\n";
			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$label='';
					if ($conf->global->SOCIETE_ADD_REF_IN_LIST)
					{
						if (($obj->client) && (!empty($obj->code_client)))
						{
							$label = $obj->code_client. ' - ';
						}
						if (($obj->fournisseur) && (!empty($obj->code_fournisseur)))
						{
							$label .= $obj->code_fournisseur. ' - ';
						}
						$label.=' '.$obj->nom.' - '.$obj->tva_intra;
					}
					else
					{
						$label=$obj->nom.' - '.$obj->tva_intra;
					}

					if ($showtype)
					{
						if ($obj->client || $obj->fournisseur) $label.=' (';
						if ($obj->client == 1 || $obj->client == 3) $label.=$langs->trans("Customer");
						if ($obj->client == 2 || $obj->client == 3) $label.=($obj->client==3?', ':'').$langs->trans("Prospect");
							if ($obj->fournisseur) $label.=($obj->client?', ':'').$langs->trans("Supplier");
								if ($obj->client || $obj->fournisseur) $label.=')';
							}
							if ($selected > 0 && $selected == $obj->rowid)
							{
								$out.= '<option value="'.$obj->rowid.'" selected="selected">'.$label.'</option>';
							}
							else
							{
								$out.= '<option value="'.$obj->rowid.'">'.$label.'</option>';
							}

							array_push($outarray, array('key'=>$obj->rowid, 'value'=>$obj->name, 'label'=>$obj->name));

							$i++;
							if (($i % 10) == 0) $out.="\n";
						}
					}
					$out.= '</select>'."\n";
				}
				else
				{
					dol_print_error($this->db);
				}

				$this->result=array('nbofthirdparties'=>$num);

				if ($outputmode) return $outarray;
				return $out;
			}

	/**
	 *     Show a confirmation HTML form or AJAX popup
	 *
	 *     @param	string		$page        	   	Url of page to call if confirmation is OK
	 *     @param	string		$title       	   	Title
	 *     @param	string		$question    	   	Question
	 *     @param 	string		$action      	   	Action
	 *	   @param	array		$formquestion	   	An array with forms complementary inputs
	 * 	   @param	string		$selectedchoice		"" or "no" or "yes"
	 * 	   @param	int			$useajax		   	0=No, 1=Yes, 2=Yes but submit page with &confirm=no if choice is No, 'xxx'=preoutput confirm box with div id=dialog-confirm-xxx
	 *     @param	int			$height          	Force height of box
	 *     @param	int			$width				Force width of box
	 *     @return 	void
	 *     @deprecated
	 */
	function form_confirmv($page, $title, $question, $action, $formquestion='', $selectedchoice="", $useajax=0, $height=170, $width=500,$lp=1)
	{
		print $this->formconfirmv($page, $title, $question, $action, $formquestion, $selectedchoice, $useajax, $height, $width,$lp);
	}
	/**
	 *     Show a confirmation HTML form or AJAX popup.
	 *     Easiest way to use this is with useajax=1.
	 *     If you use useajax='xxx', you must also add jquery code to trigger opening of box (with correct parameters)
	 *     just after calling this method. For example:
	 *       print '<script type="text/javascript">'."\n";
	 *       print 'jQuery(document).ready(function() {'."\n";
	 *       print 'jQuery(".xxxlink").click(function(e) { jQuery("#aparamid").val(jQuery(this).attr("rel")); jQuery("#dialog-confirm-xxx").dialog("open"); return false; });'."\n";
	 *       print '});'."\n";
	 *       print '</script>'."\n";
	 *
	 *     @param  	string		$page        	   	Url of page to call if confirmation is OK
	 *     @param	string		$title       	   	Title
	 *     @param	string		$question    	   	Question
	 *     @param 	string		$action      	   	Action
	 *	   @param  	array		$formquestion	   	An array with complementary inputs to add into forms: array(array('label'=> ,'type'=> , ))
	 * 	   @param  	string		$selectedchoice  	"" or "no" or "yes"
	 * 	   @param  	int			$useajax		   	0=No, 1=Yes, 2=Yes but submit page with &confirm=no if choice is No, 'xxx'=Yes and preoutput confirm box with div id=dialog-confirm-xxx
	 *     @param  	int			$height          	Force height of box
	 *     @param	int			$width				Force width of bow
	 *     @return 	string      	    			HTML ajax code if a confirm ajax popup is required, Pure HTML code if it's an html form
	 */
	function formconfirmv($page, $title, $question, $action, $formquestion='', $selectedchoice="", $useajax=0, $height=170, $width=500,$lp=1)
	{
		global $langs,$conf;
		global $useglobalvars;

		$more='';
		$formconfirm='';
		$inputok=array();
		$inputko=array();

		// Clean parameters
		$newselectedchoice=empty($selectedchoice)?"no":$selectedchoice;

		if (is_array($formquestion) && ! empty($formquestion))
		{
			// First add hidden fields and value
			foreach ($formquestion as $key => $input)
			{
				if (is_array($input) && ! empty($input))
				{
					if ($input['type'] == 'hidden')
					{
						$more.='<input type="hidden" id="'.$input['name'].'" name="'.$input['name'].'" value="'.dol_escape_htmltag($input['value']).'">'."\n";
					}
				}
			}

			// Now add questions
			$more.='<table class="paddingrightonly" width="100%">'."\n";
			$more.='<tr><td colspan="3" valign="top">'.(! empty($formquestion['text'])?$formquestion['text']:'').'</td></tr>'."\n";
			foreach ($formquestion as $key => $input)
			{
				if (is_array($input) && ! empty($input))
				{
					$size=(! empty($input['size'])?' size="'.$input['size'].'"':'');

					if ($input['type'] == 'text')
					{
						$more.='<tr><td valign="top">'.$input['label'].'</td><td valign="top" colspan="2" align="left"><input type="text" class="flat" id="'.$input['name'].'" name="'.$input['name'].'"'.$size.' value="'.$input['value'].'" /></td></tr>'."\n";
					}
					else if ($input['type'] == 'password')
					{
						$more.='<tr><td valign="top">'.$input['label'].'</td><td valign="top" colspan="2" align="left"><input type="password" class="flat" id="'.$input['name'].'" name="'.$input['name'].'"'.$size.' value="'.$input['value'].'" /></td></tr>'."\n";
					}
					else if ($input['type'] == 'select')
					{
						$more.='<tr><td valign="top" style="padding: 4px !important;">';
						if (! empty($input['label'])) $more.=$input['label'].'</td><td valign="top" colspan="2" align="left" style="padding: 4px !important;">';
							$more.=$this->selectarray($input['name'],$input['values'],$input['default'],1);
							$more.='</td></tr>'."\n";
						}
						else if ($input['type'] == 'checkbox')
						{
							$more.='<tr>';
							$more.='<td valign="top">'.$input['label'].' </td><td valign="top" align="left">';
							$more.='<input type="checkbox" class="flat" id="'.$input['name'].'" name="'.$input['name'].'"';
							if (! is_bool($input['value']) && $input['value'] != 'false') $more.=' checked="checked"';
							if (is_bool($input['value']) && $input['value']) $more.=' checked="checked"';
							if (isset($input['disabled'])) $more.=' disabled="disabled"';
							$more.=' /></td>';
							$more.='<td valign="top" align="left">&nbsp;</td>';
							$more.='</tr>'."\n";
						}
						else if ($input['type'] == 'radio')
						{
							$i=0;
							foreach($input['values'] as $selkey => $selval)
							{
								$more.='<tr>';
								if ($i==0) $more.='<td valign="top">'.$input['label'].'</td>';
								else $more.='<td>&nbsp;</td>';
								$more.='<td valign="top" width="20"><input type="radio" class="flat" id="'.$input['name'].'" name="'.$input['name'].'" value="'.$selkey.'"';
								if ($input['disabled']) $more.=' disabled="disabled"';
								$more.=' /></td>';
								$more.='<td valign="top" align="left">';
								$more.=$selval;
								$more.='</td></tr>'."\n";
								$i++;
							}
						}
						else if ($input['type'] == 'other')
						{
							$more.='<tr><td valign="top">';
							if (! empty($input['label'])) $more.=$input['label'].'</td><td valign="top" colspan="2" align="left">';
							$more.=$input['value'];
							$more.='</td></tr>'."\n";
						}
					}
				}
				$more.='</table>'."\n";
			}

			if (! empty($conf->dol_use_jmobile)) $useajax=0;
			if (empty($conf->use_javascript_ajax)) $useajax=0;

			if ($useajax)
			{
				$autoOpen=true;
				$dialogconfirm='dialog-confirm';
				$button='';
				if (! is_numeric($useajax))
				{
					$button=$useajax;
					$useajax=1;
					$autoOpen=false;
					$dialogconfirm.='-'.$button;
				}
				$pageyes=$page.(preg_match('/\?/',$page)?'&':'?').'action='.$action.'&confirm=yes';
				$pageno=($useajax == 2 ? $page.(preg_match('/\?/',$page)?'&':'?').'confirm=no':'');
				if (is_array($formquestion))
				{
					foreach ($formquestion as $key => $input)
					{
						if (isset($input['name'])) array_push($inputok,$input['name']);
						if (isset($input['inputko']) && $input['inputko'] == 1) array_push($inputko,$input['name']);
					}
				}

				$formconfirm.= '<div id="'.$dialogconfirm.'" title="'.dol_escape_htmltag($title).'" style="display: none;">';
				if (! empty($more)) {
					$formconfirm.= '<p>'.$more.'</p>';
				}
				$formconfirm.= img_help('','').' '.$question;
				$formconfirm.= '</div>';

				$formconfirm.= '<script type="text/javascript">';
				$formconfirm.='
				$(function() {
					$( "#'.$dialogconfirm.'" ).dialog({
						autoOpen: '.($autoOpen ? "true" : "false").',';
						if ($newselectedchoice == 'no')
						{
							$formconfirm.='
							open: function() {
								$(this).parent().find("button.ui-button:eq(1)").focus();
							},';
						}
						$formconfirm.='
						resizable: false,
						height: "'.$height.'",
						width: "'.$width.'",
						modal: true,
						closeOnEscape: false,
						buttons: {
							"'.dol_escape_js($langs->transnoentities("Yes")).'": function() {
								var options="";
								var inputok = '.json_encode($inputok).';
								var pageyes = "'.dol_escape_js(! empty($pageyes)?$pageyes:'').'";
								if (inputok.length>0) {
									$.each(inputok, function(i, inputname) {
										var more = "";
										if ($("#" + inputname).attr("type") == "checkbox") { more = ":checked"; }
											if ($("#" + inputname).attr("type") == "radio") { more = ":checked"; }
												var inputvalue = $("#" + inputname + more).val();
												if (typeof inputvalue == "undefined") { inputvalue=""; }
												options += "&" + inputname + "=" + inputvalue;
											});
										}
										var urljump = pageyes + (pageyes.indexOf("?") < 0 ? "?" : "") + options;
							//alert(urljump);
										if (pageyes.length > 0) { location.href = urljump; }
										$(this).dialog("close");
									},
									"'.dol_escape_js($langs->transnoentities("No")).'": function() {
										var options = "";
										var inputko = '.json_encode($inputko).';
										var pageno="'.dol_escape_js(! empty($pageno)?$pageno:'').'";
										if (inputko.length>0) {
											$.each(inputko, function(i, inputname) {
												var more = "";
												if ($("#" + inputname).attr("type") == "checkbox") { more = ":checked"; }
													var inputvalue = $("#" + inputname + more).val();
													if (typeof inputvalue == "undefined") { inputvalue=""; }
													options += "&" + inputname + "=" + inputvalue;
												});
											}
											var urljump=pageno + (pageno.indexOf("?") < 0 ? "?" : "") + options;
							//alert(urljump);
											if (pageno.length > 0) { location.href = urljump; }
											$(this).dialog("close");
										}
									}
								});

								var button = "'.$button.'";
								if (button.length > 0) {
									$( "#" + button ).click(function() {
										$("#'.$dialogconfirm.'").dialog("open");
									});
								} });
								</script>';
							}
							else
							{
								$formconfirm.= "\n<!-- begin form_confirm page=".$page." -->\n";

								$formconfirm.= '<form method="POST" action="'.$page.'" class="notoptoleftroright">'."\n";
								$formconfirm.= '<input type="hidden" name="action" value="'.$action.'">';
								$formconfirm.= '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">'."\n";

								$formconfirm.= '<table width="100%" class="valid">'."\n";

			// Line title
								$formconfirm.= '<tr class="validtitre"><td class="validtitre" colspan="3">'.img_picto('','recent').' '.$title.'</td></tr>'."\n";

			// Line form fields
								if ($more)
								{
									$formconfirm.='<tr class="valid"><td class="valid" colspan="3">'."\n";
									$formconfirm.=$more;
									$formconfirm.='</td></tr>'."\n";
								}

			// Line with question
								$formconfirm.= '<tr class="valid">';
								$formconfirm.= '<td class="valid">'.$question.'</td>';
								$formconfirm.= '<td class="valid">';
			//$formconfirm.= $this->selectyesno("confirm",$newselectedchoice);
								$formconfirm.= '</td>';
								$formconfirm.= '<td class="valid" align="center"><input class="button" type="submit" value="'.$langs->trans("Validate").'"></td>';
								$formconfirm.= '</tr>'."\n";

								$formconfirm.= '</table>'."\n";

								$formconfirm.= "</form>\n";
								$formconfirm.= '<br>';

								$formconfirm.= "<!-- end form_confirm -->\n";
							}

							return $formconfirm;
						}


						/*para modulo fiscal*/

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

		// Define list of countries to use to search VAT rates to show
		// First we defined code_country to use to find list
		if (is_object($societe_vendeuse))
		{
			$code_country="'".$societe_vendeuse->country_code."'";
		}
		else
		{
			$code_country="'".$mysoc->country_code."'";

		}
		if (! empty($conf->global->SERVICE_ARE_ECOMMERCE_200238EC))
		{
			if (! $societe_vendeuse->isInEEC() && (! is_object($societe_acheteuse) || ($societe_acheteuse->isInEEC() && ! $societe_acheteuse->isACompany())))
			{
				// We also add the buyer
				if (is_numeric($type))
				{
					if ($type == 1)
					{
						$code_country.=",'".$societe_acheteuse->country_code."'";
					}
				}
				else if (! $idprod)
				{
					$code_country.=",'".$societe_acheteuse->country_code."'";
				}
				else
				{
					$prodstatic=new Product($this->db);
					$prodstatic->fetch($idprod);
					if ($prodstatic->type == 1)
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

	function load_type_tvax($htmlname='type_tva', $selectedrate='', $societe_vendeuse='', $societe_acheteuse='', $idprod=0, $info_bits=0, $type='', $options_only=false)
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
			$code_country="'".$mysoc->country_code."'";
			 // Pour compatibilite ascendente
		}
		if (! empty($conf->global->SERVICE_ARE_ECOMMERCE_200238EC))
		// If option to have vat for end customer for services is on
		{
			if (! $societe_vendeuse->isInEEC() && (! is_object($societe_acheteuse) || ($societe_acheteuse->isInEEC() && ! $societe_acheteuse->isACompany())))
			{
				// We also add the buyer
				if (is_numeric($type))
				{
					if ($type == 1)
					// We know product is a service
					{
						$code_country.=",'".$societe_acheteuse->country_code."'";
					}
				}
				else if (! $idprod)
				// We don't know type of product
				{
					$code_country.=",'".$societe_acheteuse->country_code."'";
				}
				else
				{
					$prodstatic=new Product($this->db);
					$prodstatic->fetch($idprod);
					if ($prodstatic->type == 1)
					// We know product is a service
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

	function load_type_facture($htmlname='type_facture', $selected='', $showempty=0,$campo='rowid', $options_only=false,$filter=null)
	{
		global $langs,$conf,$mysoc;

		$return='';
		$txid=array();
		$txcode=array();
		$txlabel=array();
		$txdetail=array();

		// Define defaultnpr and defaultttx
		$defaultnpr=($info_bits & 0x01);
		$defaultnpr=(preg_match('/\*/',$selectedrate) ? 1 : $defaultnpr);
		$defaulttx=str_replace('*','',$selectedrate);

		// Now we get list
		$num = $this->load_cache_typefacture();

		if ($num > 0)
		{
			$defaulttx = $selected;
			// Disabled if seller is not subject to VAT
			$disabled=false; $title='';

			if (! $options_only) $return.= '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'"'.($disabled?' disabled="disabled"':'').$title.'>';

				if ($showempty) $return.= '<option value=-1>'.$langs->trans('Select').'</option>';

				foreach ($this->cache_type_facture as $rate)
				{
					$lView = true;
					if ($filter !== null)
					{
						if ($rate['type_fact'] != $filter) $lView = false;
					}
					if ($lView)
					{
						$return.= '<option value="'.$rate[$campo];
					//$return.= $rate['nprtva'] ? '*': '';
						$return.= '"';
						if ($rate[$campo] == $defaulttx)
						{
							$return.= ' selected="selected"';
						}
						$return.= '>'.$rate['code'];
					//$return.= $rate['nprtva'] ? ' *': '';
						$return.= $rate['label'] ? ' '.$rate['label']: '';

						$return.= '</option>';

						$this->type_facture_id[]    = $rate['rowid'];
						$this->type_facture_code[]  = $rate['code'];
						$this->type_facture_label[] = $rate['label'];
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
	function load_cache_typefacture()
	{
		global $langs,$mysoc;

		$num = count($this->cache_type_facture);
		if ($num > 0) return $num;    // Cache deja charge

		$sql  = "SELECT DISTINCT t.rowid, t.code, t.label, t.detail, t.type, t.type_fact";
		$sql.= " FROM ".MAIN_DB_PREFIX."c_type_facture as t ";
		$sql.= " WHERE t.active = 1";
		$sql.= " AND t.fk_pays = ".$mysoc->country_id;
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
					$this->cache_type_facture[$i]['rowid']  = $obj->rowid;
					$this->cache_type_facture[$i]['code'] = $obj->code;
					$this->cache_type_facture[$i]['label'] = $obj->label;
					$this->cache_type_facture[$i]['detail'] = $obj->detail;
					$this->cache_type_facture[$i]['type'] = $obj->type;
					$this->cache_type_facture[$i]['type_fact'] = $obj->type_fact;
				}
				return $num;
			}
			else
			{
				$this->error = '<font class="error">'.$langs->trans("ErrorNoTypeFactureDefined",$country_code).'</font>';
				return -1;
			}
		}
		else
		{
			$this->error = '<font class="error">'.$this->db->error().'</font>';
			return -2;
		}
	}

	function load_type_tva($htmlname='type_tva', $selected='', $showempty=0,$campo='rowid', $options_only=false,$filter=0)
	{
		global $langs,$conf,$mysoc;

		$return='';
		$txid 	  = array();
		$txcode  = array();
		$txlabel  = array();
		$txdetail = array();


		// Now we get list
		$num = $this->load_cache_typetva();

		if ($num > 0)
		{
			$defaulttx = $selected;
			// Disabled if seller is not subject to VAT
			$disabled=false; $title='';

			if (! $options_only) $return.= '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'"'.($disabled?' disabled="disabled"':'').$title.'>';

				if ($showempty) $return.= '<option value=-1>'.$langs->trans('Select').'</option>';
				foreach ($this->cache_type_tva as $rate)
				{
					$lView = true;
					if ($filter !== null)
					{
						if ($rate['type'] != $filter) $lView = false;
					}
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

						$this->type_tva_id[]    = $rate['rowid'];
						$this->type_tva_code[]  = $rate['code'];
						$this->type_tva_label[] = $rate['label'];
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
	function load_cache_typetva()
	{
		global $langs,$mysoc;

		$num = count($this->cache_type_tva);
		if ($num > 0) return $num;
		// Cache deja charge

		$sql  = "SELECT DISTINCT t.rowid, t.code, t.label";
		$sql.= " FROM ".MAIN_DB_PREFIX."c_type_tva as t ";
		$sql.= " WHERE t.active = 1";
		$sql.= " AND t.fk_pays = ".$mysoc->country_id;
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
					$this->cache_type_tva[$i]['rowid']  = $obj->rowid;
					$this->cache_type_tva[$i]['code'] = $obj->code;
					$this->cache_type_tva[$i]['label'] = $obj->label;
				}
				return $num;
			}
			else
			{
				$this->error = '<font class="error">'.$langs->trans("ErrorNoTypeTvaDefined",$country_code).'</font>';
				return -1;
			}
		}
		else
		{
			$this->error = '<font class="error">'.$this->db->error().'</font>';
			return -2;
		}
	}

	/*purchase*/

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
		include_once(DOL_DOCUMENT_ROOT.'/core/tpl2/frames.tpl.php');

			//include_once(DOL_DOCUMENT_ROOT.'/purchase/tpl/framesfourn.tpl.php');

		if (! empty($conf->use_javascript_ajax) && ! empty($conf->global->PRODUIT_USE_SEARCH_TO_SELECT))
		{
				// mode=2 means suppliers products
			$urloption=($socid > 0?'socid='.$socid.'&':'').'htmlname='.$htmlname.'&outjson=1&price_level='.$price_level.'&type='.$filtertype.'&mode=2&status='.$status.'&finished='.$finished;
			print ajax_autocompleter('', $htmlname, DOL_URL_ROOT.'/product/ajax/productsv.php', $urloption, $conf->global->PRODUIT_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
			print ($hidelabel?'':$langs->trans("RefOrLabel").' : ').'<input type="text" size="16" name="search_'.$htmlname.'" id="search_'.$htmlname.'" onblur="revisaFramefourn(this.value);">';
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
		$reshook=$hookmanager->executeHooks('showLinkedObjectBlockfractal',$parameters,$object,$action);
		    // Note that $action and $object may have been modified by hook

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
				else if ($objecttype == 'poaprev')   {
					$tplpath = 'budgetgob/execution';
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

	/*monprojet*/
	/**
	 *    Return a HTML area with the reference of object and a navigation bar for a business object
	 *    To add a particular filter on select, you must set $object->next_prev_filter to SQL criteria.
	 *
	 *    @param	object	$object			Object to show
	 *    @param	string	$paramid   		Name of parameter to use to name the id into the URL link
	 *    @param	string	$morehtml  		More html content to output just before the nav bar
	 *    @param	int		$shownav	  	Show Condition (navigation is shown if value is 1)
	 *    @param	string	$fieldid   		Nom du champ en base a utiliser pour select next et previous (we make the select max and min on this field)
	 *    @param	string	$fieldref   	Nom du champ objet ref (object->ref) a utiliser pour select next et previous
	 *    @param	string	$morehtmlref  	Code html supplementaire a afficher apres ref
	 *    @param	string	$moreparam  	More param to add in nav link url.
	 *	  @param	int		$nodbprefix		Do not include DB prefix to forge table name
	 * 	  @return	string    				Portion HTML avec ref + boutons nav
	 */
	function showrefnavmon ($object,$paramid,$morehtml='',$shownav=1,$fieldid='rowid',$fieldref='ref',$morehtmlref='',$moreparam='',$nodbprefix=0)
	{
		global $langs,$conf,$db;

		$ret='';
		if (empty($fieldid))  $fieldid='rowid';
		if (empty($fieldref)) $fieldref='ref';
		$refstatic = $object->$fieldref;
		//print "paramid=$paramid,morehtml=$morehtml,shownav=$shownav,$fieldid,$fieldref,$morehtmlref,$moreparam";
		$object->load_previous_next_refadd((isset($object->next_prev_filter)?$object->next_prev_filter:''),$fieldid,$object,$nodbprefix);

		//$previous_ref = $object->ref_previous?'<a data-role="button" data-icon="arrow-l" data-iconpos="left" href="'.$_SERVER["PHP_SELF"].'?'.$paramid.'='.urlencode($object->ref_previous).$moreparam.'">'.(empty($conf->dol_use_jmobile)?img_picto($langs->trans("Previous"),'previous.png'):'&nbsp;').'</a>':'';
		//$next_ref     = $object->ref_next?'<a data-role="button" data-icon="arrow-r" data-iconpos="right" href="'.$_SERVER["PHP_SELF"].'?'.$paramid.'='.urlencode($object->ref_next).$moreparam.'">'.(empty($conf->dol_use_jmobile)?img_picto($langs->trans("Next"),'next.png'):'&nbsp;').'</a>':'';
		$previous_ref = $object->ref_previous?'<a data-role="button" data-icon="arrow-l" data-iconpos="left" href="'.$_SERVER["PHP_SELF"].'?'.$paramid.'='.urlencode($object->ref_previous).$moreparam.'">'.(empty($conf->dol_use_jmobile)?'<':'&nbsp;').'</a>':'';
		$next_ref     = $object->ref_next?'<a data-role="button" data-icon="arrow-r" data-iconpos="right" href="'.$_SERVER["PHP_SELF"].'?'.$paramid.'='.urlencode($object->ref_next).$moreparam.'">'.(empty($conf->dol_use_jmobile)?'>':'&nbsp;').'</a>':'';

		//print "xx".$previous_ref."x".$next_ref;
		$ret.='<div style="vertical-align: middle"><div class="inline-block floatleft refid'.(($shownav && ($previous_ref || $next_ref))?' refidpadding':'').'">';
		$ret.=dol_htmlentities($refstatic);
		if ($morehtmlref)
		{
			$ret.=' '.$morehtmlref;
		}
		$ret.='</div>';

		if ($previous_ref || $next_ref || $morehtml)
		{
			$ret.='<div class="pagination"><ul>';
		}
		if ($morehtml)
		{
			//$ret.='</td><td class="paddingrightonly" align="right">'.$morehtml;
			$ret.='<li class="noborder litext">'.$morehtml.'</li>';
		}
		if ($shownav && ($previous_ref || $next_ref))
		{
			//$ret.='</td><td class="nobordernopadding" align="center" width="20">'.$previous_ref.'</td>';
			//$ret.='<td class="nobordernopadding" align="center" width="20">'.$next_ref;
			$ret.='<li class="pagination">'.$previous_ref.'</li>';
			$ret.='<li class="pagination">'.$next_ref.'</li>';
		}
		if ($previous_ref || $next_ref || $morehtml)
		{
			//$ret.='</td></tr></table>';
			$ret.='</ul></div>';
		}
		$ret.='</div>';

		return $ret;
	}

	/**
	 *    Return a HTML area with the reference of object and a navigation bar for a business object
	 *    To add a particular filter on select, you must set $object->next_prev_filter to SQL criteria.
	 *
	 *    @param	object	$object			Object to show
	 *    @param	string	$paramid   		Name of parameter to use to name the id into the URL next/previous link
	 *    @param	string	$morehtml  		More html content to output just before the nav bar
	 *    @param	int		$shownav	  	Show Condition (navigation is shown if value is 1)
	 *    @param	string	$fieldid   		Name of field id into database to use for select next and previous (we make the select max and min on this field)
	 *    @param	string	$fieldref   	Name of field ref of object (object->ref) to show or 'none' to not show ref.
	 *    @param	string	$morehtmlref  	More html to show after ref
	 *    @param	string	$moreparam  	More param to add in nav link url.
	 *	  @param	int		$nodbprefix		Do not include DB prefix to forge table name
	 *	  @param	string	$morehtmlleft	More html code to show before ref
	 *	  @param	string	$morehtmlstatus	More html code to show under navigation arrows (status place)
	 *	  @param	string	$morehtmlright	More html code to show after ref
	 * 	  @return	string    				Portion HTML with ref + navigation buttons
	 */
	function showrefnavadd($object,$paramid,$morehtml='',$shownav=1,$fieldid='rowid',$fieldref='ref',$morehtmlref='',$moreparam='',$nodbprefix=0,$morehtmlleft='',$morehtmlstatus='',$morehtmlright='')
	{
		global $langs,$conf,$socid;

		$ret='';
		if (empty($fieldid))  $fieldid='rowid';
		if (empty($fieldref)) $fieldref='ref';
		$refactual = $object->$fieldref;
		$objadd = clone $object;
			//verificamos el siguiente y anterior
		$tasksarray = $objadd->getTasksArray(0, 0, $object->fk_project, $socid, $modetask,'',-1,'',0,0,0,1,0,'',$modepay);
		$taskant= 0;
		$tasknext=0;
		$taskidant = 0;
		$taskidnext = 0;
		foreach ((array) $tasksarray AS $j => $obj)
		{
			if ($object->id == $obj->id)
			{
				$taskidant = $taskant;
				$taskidnext = $tasksarray[$j+1]->id;
				unset($tasksarray);
			}
			$taskant = $obj->id;
		}
		$object->ref_previous = $taskidant;
		$object->ref_next = $taskidnext;
		//print "paramid=$paramid,morehtml=$morehtml,shownav=$shownav,$fieldid,$fieldref,$morehtmlref,$moreparam";
		//$object->load_previous_next_refadd((isset($object->next_prev_filter)?$object->next_prev_filter:''),$fieldid,$object,$nodbprefix);

		//$previous_ref = $object->ref_previous?'<a data-role="button" data-icon="arrow-l" data-iconpos="left" href="'.$_SERVER["PHP_SELF"].'?'.$paramid.'='.urlencode($object->ref_previous).$moreparam.'">'.(empty($conf->dol_use_jmobile)?img_picto($langs->trans("Previous"),'previous.png'):'&nbsp;').'</a>':'';
		//$next_ref     = $object->ref_next?'<a data-role="button" data-icon="arrow-r" data-iconpos="right" href="'.$_SERVER["PHP_SELF"].'?'.$paramid.'='.urlencode($object->ref_next).$moreparam.'">'.(empty($conf->dol_use_jmobile)?img_picto($langs->trans("Next"),'next.png'):'&nbsp;').'</a>':'';
		$previous_ref = $object->ref_previous?'<a data-role="button" data-icon="arrow-l" data-iconpos="left" href="'.$_SERVER["PHP_SELF"].'?'.$paramid.'='.urlencode($object->ref_previous).$moreparam.'">'.(empty($conf->dol_use_jmobile)?'&lt;':'&nbsp;').'</a>':'<span class="inactive">'.(empty($conf->dol_use_jmobile)?'&lt;':'&nbsp;').'</span>';
		$next_ref     = $object->ref_next?'<a data-role="button" data-icon="arrow-r" data-iconpos="right" href="'.$_SERVER["PHP_SELF"].'?'.$paramid.'='.urlencode($object->ref_next).$moreparam.'">'.(empty($conf->dol_use_jmobile)?'&gt;':'&nbsp;').'</a>':'<span class="inactive">'.(empty($conf->dol_use_jmobile)?'&gt;':'&nbsp;').'</span>';

		//print "xx".$previous_ref."x".$next_ref;
		$ret.='<div style="vertical-align: middle">';

		if ($morehtmlleft) $ret.='<div class="inline-block floatleft">'.$morehtmlleft.'</div>';

		$ret.='<div class="inline-block floatleft valignmiddle refid'.(($shownav && ($previous_ref || $next_ref))?' refidpadding':'').'">';

		// For thirdparty, contact, user, member, the ref is the id, so we show something else
		if ($object->element == 'societe')
		{
			$ret.=dol_htmlentities($object->name);
		}
		else if (in_array($object->element, array('contact', 'user', 'member')))
		{
			$ret.=dol_htmlentities($object->getFullName($langs));
		}
		else if ($fieldref != 'none')
		{
			$ret.=dol_htmlentities($refactual);
		}
		if ($morehtmlref)
		{
			$ret.=' '.$morehtmlref;
		}
		$ret.='</div>';

		if ($morehtmlright) $ret.='<div class="inline-block floatleft">'.$morehtmlright.'</div>';

		if ($previous_ref || $next_ref || $morehtml)
		{
			$ret.='<div class="pagination"><ul>';
		}
		if ($morehtml)
		{
			$ret.='<li class="noborder litext">'.$morehtml.'</li>';
		}
		if ($shownav && ($previous_ref || $next_ref))
		{
			$ret.='<li class="pagination">'.$previous_ref.'</li>';
			$ret.='<li class="pagination">'.$next_ref.'</li>';
		}
		if ($previous_ref || $next_ref || $morehtml)
		{
			$ret.='</ul></div>';
		}
		if ($morehtmlstatus) $ret.='<div class="statusref">'.$morehtmlstatus.'</div>';
		$ret.='</div>';

		return $ret;
	}

	//select pedido fourniseur det
	//select item
	/**
	 *  Return list of products for commande fournisseur in Ajax if Ajax activated or go to select_produits_list
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
	function select_commande_fourn_v($selected='', $htmlname='productid', $filtertype='', $limit=20, $price_level=0, $status=1, $finished=2, $selected_input_value='', $hidelabel=0, $ajaxoptions=array(),$socid=0,$action='',$filterstatic='',$filtercategory='',$hidesocinlabel=0)
	{

		global $langs,$conf;
		//include_once(DOL_DOCUMENT_ROOT.'/budget/tpl/framesitem.tpl.php');
		$price_level = (! empty($price_level) ? $price_level : 0);

		if (! empty($conf->use_javascript_ajax) && ! empty($conf->global->COMMANDE_FOURNDET_USE_SEARCH_TO_SELECT))
		{
			$placeholder='';

			if ($selected && empty($selected_input_value))
			{
				require_once DOL_DOCUMENT_ROOT.'/purchase/class/fournisseurcommandeext.class.php';
				$commandedet = new CommandeFournisseurLigneext($this->db);
				$commandedet->fetch($selected);
				$selected_input_value=$commandedet->ref;
			}
			// mode=1 means customers products
			$urloption='htmlname='.$htmlname.'&outjson=1&price_level='.$price_level.'&type='.$filtertype.'&mode=1&status='.$status.'&finished='.$finished.'&selected='.$selected.'&filterstatic='.$filterstatic.'&hidesocinlabel='.$hidesocinlabel;

			print ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/purchase/ajax/commandefourn.php', $urloption, $conf->global->COMMANDE_FOURNDET_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
			if (empty($hidelabel)) print $langs->trans("RefOrLabel").' : ';
				elseif ($hidelabel > 1) {
					if (! empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $placeholder=' placeholder="'.$langs->trans("RefOrLabel").'"';
					else $placeholder=' title="'.$langs->trans("RefOrLabel").'"';
					if ($hidelabel == 2) {
						print img_picto($langs->trans("Search"), 'search');
					}
				}
				print '<input type="text" class="theight20" size="10" name="search_'.$htmlname.'" id="search_'.$htmlname.'" value="'.$selected_input_value.'"'.$placeholder.'/>';

				if ($hidelabel == 3) {
					print img_picto($langs->trans("Search"), 'search');
				}
			}
			else
			{
				$outjson = 0;
				print $this->select_commande_fourn_list_v($selected,$htmlname,$filtertype,$limit,$price_level,'',$status,$finished,$outjson,$socid,$filterstatic,$filtercategory,$hidesocinlabel);
			}
		}

	//select contrat fourniseur det
	//select item
	/**
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
	function select_contrat_fourn_v($selected='', $htmlname='productid', $filtertype='', $limit=20, $price_level=0, $status=1, $finished=2, $selected_input_value='', $hidelabel=0, $ajaxoptions=array(),$socid=0,$fk_projet=0,$action='',$filterstatic='',$filtercategory='',$hidesocinlabel=0)
	{

		global $langs,$conf;
		//include_once(DOL_DOCUMENT_ROOT.'/budget/tpl/framesitem.tpl.php');
		$price_level = (! empty($price_level) ? $price_level : 0);

		if (! empty($conf->use_javascript_ajax) && ! empty($conf->global->CONTRAT_FOURNDET_USE_SEARCH_TO_SELECT))
		{
			$placeholder='';

			if ($selected && empty($selected_input_value))
			{
				require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
				$contratdet = new ContratLigne($this->db);
				$contratdet->fetch($selected);
				$selected_input_value=$contratdet->ref;
			}
			// mode=1 means customers products
			$urloption='htmlname='.$htmlname.'&outjson=1&price_level='.$price_level.'&type='.$filtertype.'&mode=1&status='.$status.'&finished='.$finished.'&selected='.$selected.'&filterstatic='.$filterstatic.'&hidesocinlabel='.$hidesocinlabel;

			print ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/monprojet/ajax/contratfourn.php', $urloption, $conf->global->CONTRAT_FOURNDET_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
			if (empty($hidelabel)) print $langs->trans("RefOrLabel").' : ';
				else if ($hidelabel > 1) {
					if (! empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $placeholder=' placeholder="'.$langs->trans("RefOrLabel").'"';
					else $placeholder=' title="'.$langs->trans("RefOrLabel").'"';
					if ($hidelabel == 2) {
						print img_picto($langs->trans("Search"), 'search');
					}
				}
				print '<input type="text" class="theight20" size="10" name="search_'.$htmlname.'" id="search_'.$htmlname.'" value="'.$selected_input_value.'"'.$placeholder.'/>';

				if ($hidelabel == 3) {
					print img_picto($langs->trans("Search"), 'search');
				}
			}
			else
			{
				$outjson = 0;
				print $this->select_contrat_fourn_list_v($selected,$htmlname,$filtertype,$limit,$price_level,'',$status,$finished,$outjson,$socid,$fk_projet,$filterstatic,$filtercategory,$hidesocinlabel);
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
	 *  @param      int		$socid     		Thirdparty Id (to get also price dedicated to this customer)
	 *  @return     array    				Array of keys for json
	 */
	function select_commande_fourn_list_v($selected='',$htmlname='itemid',$filtertype='',$limit=20,$price_level=0,$filterkey='',$status=1,$finished=2,$outputmode=0,$socid=0,$filterstatic='',$filtercategory='',$hidesocinlabel=0)
	{
		global $langs,$conf,$user,$db;
		if ($filtercategory)
		{
			require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
			$categorie = new categorie($db);
		}
		$out='';
		$outarray=array();

		$sql = "SELECT ";
		$sql.= " d.rowid, d.fk_product, d.label, d.ref, d.description, d.description AS detail ";
		$sql.= " , t.ref AS reff, t.fk_soc ";
		$sql.= " , s.nom AS nomsoc ";
		$sql.= " , p.fk_product_type ";
		$sql.= " FROM ".MAIN_DB_PREFIX."commande_fournisseurdet as d";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."commande_fournisseur as t ON d.fk_commande = t.rowid";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."societe as s ON t.fk_soc = s.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product as p ON d.fk_product = p.rowid";
		$sql.= ' WHERE t.entity IN ('.getEntity('commande_fournisseur', 1).')';
		if ($socid>0)
			$sql.= " AND t.fk_soc = ".$socid;
		// Add criteria on ref/label
		if ($filterkey != '')
		{
			$sql.=' AND (';
			$prefix=empty($conf->global->COMMANDE_FOURNDET_DONOTSEARCH_ANYWHERE)?'%':'';
			// Can use index if PRODUCT_DONOTSEARCH_ANYWHERE is on
			// For natural search
			$scrit = explode(' ', $filterkey);
			$i=0;
			if (count($scrit) > 1) $sql.="(";
			foreach ($scrit as $crit)
			{
				if ($i > 0) $sql.=" AND ";
				$sql.="(t.ref LIKE '".$db->escape($prefix.$crit)."%' OR d.ref LIKE '".$db->escape($prefix.$crit)."%' OR d.description LIKE '".$db->escape($prefix.$crit)."%' OR d.label LIKE '".$db->escape($prefix.$crit)."%'";
				$sql.=")";
				$i++;
			}
			if (count($scrit) > 1) $sql.=")";
			$sql.=')';
		}
		if ($filterstatic) $sql.= $filterstatic;

		$sql.= $db->order("d.label, s.nom, t.ref");
		$sql.= $db->plimit($limit);


		// Build output string
		dol_syslog(get_class($this)."::select_commande_fourn_list_v search items", LOG_DEBUG);
		$result=$this->db->query($sql);
		if ($result)
		{
			//require_once DOL_DOCUMENT_ROOT.'/budget/class/items.class.php';
			$num = $this->db->num_rows($result);

			$out.='<select class="flat" name="'.$htmlname.'" id="'.$htmlname.'">';
			$out.='<option value="0" selected>&nbsp;</option>';

			$i = 0;
			while ($num && $i < $num)
			{
				$lView = true;
				$opt = '';
				$optJson = array();
				$objp = $this->db->fetch_object($result);
				if ($filtercategory)
				{
					$lView = false;
					$row = $categorie->containing($objp->fk_product, 'product', 'id');
					if (in_array($filtercategory,$row))
						$lView = true;
				}
				if ($lView)
				{
					$this->constructCommandeFournListOption($objp, $opt, $optJson, $price_level, $selected,$hidesocinlabel);
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
	 * constructCommandeFournListOption
	 *
	 * @param 	resultset	$objp			    Resultset of fetch
	 * @param 	string		$opt			    Option (var used for returned value in string option format)
	 * @param 	string		$optJson		    Option (var used for returned value in json format)
	 * @param 	int			$price_level	    Price level
	 * @param 	string		$selected		    Preselected value
	 * @param   int         $hidepriceinlabel   Hide price in label
	 * @return	void
	 */
	private function constructCommandeFournListOption(&$objp, &$opt, &$optJson, $price_level, $selected, $hidesocinlabel=0)
	{
		global $langs,$conf,$user,$db;

		$outkey='';
		$outval='';
		$outref='';
		$outlabel='';
		$outdesc='';
		$outbarcode='';
		$outtype='';
		$outprice_ht='';
		$outprice_ttc='';
		$outpricebasetype='';
		$outtva_tx='';
		$outqty=1;
		$outdiscount=0;

		$maxlengtharticle=(empty($conf->global->COMMANDE_FOURNDET_MAX_LENGTH_COMBO)?48:$conf->global->COMMANDE_FOURNDET_MAX_LENGTH_COMBO);

		$label=$objp->label;
		if (! empty($objp->label_translated)) $label=$objp->label_translated;
		if (! empty($filterkey) && $filterkey != '') $label=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$label,1);

		$outkey=$objp->rowid;
		$outref=$objp->ref;
		$outlabel=$objp->label;
		$outdesc=$objp->detail;
		$opt = '<option value="'.$objp->rowid.'"';
		$opt.= ($objp->rowid == $selected)?' selected':'';
		$opt.= '>';

		$opt.= dol_trunc($label,$maxlengtharticle);
		if ($hidesocinlabel)
			$opt.= ' - '.$objp->nomsoc;
		$opt.= ' - '.$objp->reff;
		//$opt.= ' - '.$objp->ref;

		//$opt.=' - '.dol_trunc($outdesc,$maxlengtharticle).' - ';

		$objRef = $objp->ref;
		if (! empty($filterkey) && $filterkey != '') $objRef=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$objRef,1);

		$outval.= dol_trunc($label,$maxlengtharticle);
		$outval.= ' - '.$objp->ref;
		if ($hidesocinlabel)
			$outval.= ' - '.$objp->nomsoc;

		$outval.= ' - '.$objp->reff;

		//$outval.=' - '.dol_trunc($outdesc,$maxlengtharticle).' - ';

		$found=0;
		$opt.= "</option>\n";
		$optJson = array('key'=>$outkey, 'value'=>$outref, 'label'=>$outval, 'label2'=>$outlabel, 'desc'=>$outdesc);
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
	 *  @param      int		$socid     		Thirdparty Id (to get also price dedicated to this customer)
	 *  @return     array    				Array of keys for json
	 */
	function select_contrat_fourn_list_v($selected='',$htmlname='itemid',$filtertype='',$limit=20,$price_level=0,$filterkey='',$status=1,$finished=2,$outputmode=0,$socid=0,$fk_projet=0,$filterstatic='',$filtercategory='',$hidesocinlabel=0)
	{
		global $langs,$conf,$user,$db;
		if ($filtercategory)
		{
			require_once DOL_DOCUMENT_ROOT.'/categories/class/categorie.class.php';
			$categorie = new categorie($db);
		}
		$out='';
		$outarray=array();

		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.tms,";
		$sql.= " t.fk_contrat,";
		$sql.= " t.fk_product,";
		$sql.= " t.statut,";
		$sql.= " t.label,";			// This field is not used. Only label of product
		$sql.= " p.ref as product_ref,";
		$sql.= " p.label as product_label,";
		$sql.= " p.description as product_desc,";
		$sql.= " p.fk_product_type as product_type,";
		$sql.= " t.description,";
		$sql.= " t.date_commande,";
		$sql.= " t.date_ouverture_prevue as date_ouverture_prevue,";
		$sql.= " t.date_ouverture as date_ouverture,";
		$sql.= " t.date_fin_validite as date_fin_validite,";
		$sql.= " t.date_cloture as date_cloture,";
		$sql.= " t.tva_tx,";
		$sql.= " t.localtax1_tx,";
		$sql.= " t.localtax2_tx,";
		$sql.= " t.qty,";
		$sql.= " t.remise_percent,";
		$sql.= " t.remise,";
		$sql.= " t.fk_remise_except,";
		$sql.= " t.subprice,";
		$sql.= " t.price_ht,";
		$sql.= " t.total_ht,";
		$sql.= " t.total_tva,";
		$sql.= " t.total_localtax1,";
		$sql.= " t.total_localtax2,";
		$sql.= " t.total_ttc,";
		$sql.= " t.fk_product_fournisseur_price as fk_fournprice,";
		$sql.= " t.buy_price_ht as pa_ht,";
		$sql.= " t.info_bits,";
		$sql.= " t.fk_user_author,";
		$sql.= " t.fk_user_ouverture,";
		$sql.= " t.fk_user_cloture,";
		$sql.= " t.commentaire,";
		$sql.= " t.fk_unit";
		$sql.= " ,c.ref";
		$sql.= " ,s.nom AS nomsoc";
		$sql.= " FROM ".MAIN_DB_PREFIX."contratdet as t LEFT JOIN ".MAIN_DB_PREFIX."product as p ON p.rowid = t.fk_product";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."contrat as c ON t.fk_contrat = c.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON c.fk_soc = s.rowid";
		$sql.= ' WHERE c.entity IN ('.getEntity('contrat', 1).')';
		if ($socid>0)
			$sql.= " AND t.fk_soc = ".$socid;
		// Add criteria on ref/label
		if ($filterkey != '')
		{
			$sql.=' AND (';
			$prefix=empty($conf->global->CONTRAT_FOURNDET_DONOTSEARCH_ANYWHERE)?'%':'';
			// Can use index if PRODUCT_DONOTSEARCH_ANYWHERE is on
			// For natural search
			$scrit = explode(' ', $filterkey);
			$i=0;
			if (count($scrit) > 1) $sql.="(";
			foreach ($scrit as $crit)
			{
				if ($i > 0) $sql.=" AND ";
				$sql.="(c.ref LIKE '".$db->escape($prefix.$crit)."%' OR t.label LIKE '".$db->escape($prefix.$crit)."%' OR p.description LIKE '".$db->escape($prefix.$crit)."%' OR p.label LIKE '".$db->escape($prefix.$crit)."%'";
				$sql.=")";
				$i++;
			}
			if (count($scrit) > 1) $sql.=")";
			$sql.=')';
		}
		if ($filterstatic) $sql.= $filterstatic;
		$sql.= $db->order("t.label");
		$sql.= $db->plimit($limit);

		// Build output string
		dol_syslog(get_class($this)."::select_contrat_fourn_list_v search items", LOG_DEBUG);
		$result=$this->db->query($sql);
		if ($result)
		{
			//require_once DOL_DOCUMENT_ROOT.'/budget/class/items.class.php';
			$num = $this->db->num_rows($result);

			$out.='<select class="flat" name="'.$htmlname.'" id="'.$htmlname.'">';
			$out.='<option value="0" selected>&nbsp;</option>';

			$i = 0;
			while ($num && $i < $num)
			{
				$lView = true;
				$opt = '';
				$optJson = array();
				$objp = $this->db->fetch_object($result);
				if ($filtercategory)
				{
					$lView = false;
					if ($objp->fk_product > 0)
					{
						$row = $categorie->containing($objp->fk_product, 'product', 'id');
						if (in_array($filtercategory,$row)) $lView = true;
					}
					else
						$lView = true;
				}
				if ($lView)
				{
					$this->constructContratFournListOption($objp, $opt, $optJson, $price_level, $selected,$hidesocinlabel);
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
	 * constructCommandeFournListOption
	 *
	 * @param 	resultset	$objp			    Resultset of fetch
	 * @param 	string		$opt			    Option (var used for returned value in string option format)
	 * @param 	string		$optJson		    Option (var used for returned value in json format)
	 * @param 	int			$price_level	    Price level
	 * @param 	string		$selected		    Preselected value
	 * @param   int         $hidepriceinlabel   Hide price in label
	 * @return	void
	 */
	private function constructContratFournListOption(&$objp, &$opt, &$optJson, $price_level, $selected, $hidesocinlabel=0)
	{
		global $langs,$conf,$user,$db;

		$outkey='';
		$outval='';
		$outref='';
		$outlabel='';
		$outdesc='';
		$outbarcode='';
		$outtype='';
		$outprice_ht='';
		$outprice_ttc='';
		$outpricebasetype='';
		$outtva_tx='';
		$outqty=1;
		$outdiscount=0;

		$maxlengtharticle=(empty($conf->global->COMMANDE_FOURNDET_MAX_LENGTH_COMBO)?48:$conf->global->COMMANDE_FOURNDET_MAX_LENGTH_COMBO);

		$label=$objp->description;
		if (! empty($objp->label_translated)) $label=$objp->label_translated;
		if (! empty($filterkey) && $filterkey != '') $label=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$label,1);

		$outkey=$objp->rowid;
		$outref=$objp->product_ref;
		$outlabel=$objp->description;
		$outdesc=$objp->description;
		$opt = '<option value="'.$objp->rowid.'"';
		$opt.= ($objp->rowid == $selected)?' selected':'';
		$opt.= '>';

		$opt.= dol_trunc($label,$maxlengtharticle);
		if ($hidesocinlabel)
			$opt.= ' - '.$objp->nomsoc;
		$opt.= ' - '.$objp->ref;
		//$opt.= ' - '.$objp->ref;

		//$opt.=' - '.dol_trunc($outdesc,$maxlengtharticle).' - ';

		$objRef = $objp->ref;
		if (! empty($filterkey) && $filterkey != '') $objRef=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$objRef,1);

		$outval.= dol_trunc($label,$maxlengtharticle);
		$outval.= ' - '.$objp->ref;
		if ($hidesocinlabel)
			$outval.= ' - '.$objp->nomsoc;

		$outval.= ' - '.$objp->reff;

		//$outval.=' - '.dol_trunc($outdesc,$maxlengtharticle).' - ';

		$found=0;
		$opt.= "</option>\n";
		$optJson = array('key'=>$outkey, 'value'=>$outref, 'label'=>$outval, 'label2'=>$outlabel, 'desc'=>$outdesc);
	}
	//select solicitud almacen det
	//select item
	/**
	 *  Return list of products for commande fournisseur in Ajax if Ajax activated or go to select_produits_list
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
	function select_sol_almacendet_v($selected='', $htmlname='fk', $filtertype='', $limit=20, $status=1, $selected_input_value='', $hidelabel=0, $ajaxoptions=array(),$fk_sol=0,$balance=0,$action='')
	{
		global $langs,$conf;

		$price_level = (! empty($price_level) ? $price_level : 0);

		if (! empty($conf->use_javascript_ajax) && ! empty($conf->global->ALMACEN_DET_USE_SEARCH_TO_SELECT))
		{
			$placeholder='';

			if ($selected && empty($selected_input_value))
			{
				require_once DOL_DOCUMENT_ROOT.'/almacen/class/solalmacendetadd.class.php';
				require_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
				$solicitud = new Solalmacendetadd($this->db);
				$product 	= new Product($this->db);
				$solicitud->fetch($selected);
				$product->fetch($solicitud->fk_product);
				$selected_input_value=$product->ref;
			}
			// mode=1 means customers products
			$urloption='htmlname='.$htmlname.'&outjson=1&type='.$filtertype.'&status='.$status.'&selected='.$selected;

			print ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/almacen/ajax/almacendet.php', $urloption, $conf->global->ALMACEN_DET_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
			if (empty($hidelabel)) print $langs->trans("RefOrLabel").' : ';
				else if ($hidelabel > 1) {
					if (! empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $placeholder=' placeholder="'.$langs->trans("RefOrLabel").'"';
					else $placeholder=' title="'.$langs->trans("RefOrLabel").'"';
					if ($hidelabel == 2) {
						print img_picto($langs->trans("Search"), 'search');
					}
				}
				print '<input type="text" class="theight20" size="10" name="search_'.$htmlname.'" id="search_'.$htmlname.'" value="'.$selected_input_value.'"'.$placeholder.'/>';

				if ($hidelabel == 3) {
					print img_picto($langs->trans("Search"), 'search');
				}
			}
			else
			{
				print $this->select_sol_almacendet_list_v($selected,$htmlname,$filtertype,$limit,'',$status,0,$fk_sol,$balance);
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
	 *  @param      int		$socid     		Thirdparty Id (to get also price dedicated to this customer)
	 *  @return     array    				Array of keys for json
	 */
	function select_sol_almacendet_list_v($selected='',$htmlname='fk',$filtertype='',$limit=20,$filterkey='',$status=1,$outputmode=0,$fk_sol=0,$balance=0)
	{
		global $langs,$conf,$user,$db;

		if ($balance)
		{
			require_once DOL_DOCUMENT_ROOT.'/almacen/class/mouvementstockext.class.php';
			$objstock = new Mouvementstockext($this->db);
		}

		$out='';
		$outarray=array();

		$sql = "SELECT ";
		$sql.= " d.rowid, d.fk_product, p.label, p.ref, p.description, d.qty_livree ";
		$sql.= " , t.ref AS reff, t.description as detail ";
		$sql.= " FROM ".MAIN_DB_PREFIX."sol_almacendet as d";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product as p ON d.fk_product = p.rowid";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."sol_almacen as t ON d.fk_almacen = t.rowid";
		$sql.= ' WHERE t.entity IN ('.getEntity('sol_almacen', 1).')';
		if ($fk_sol>0) $sql.= " AND t.rowid = ".$fk_sol;
		$sql.= " AND t.statut = 2 ";
		// Add criteria on ref/label
		if ($filterkey != '')
		{
			$sql.=' AND (';
			$prefix=empty($conf->global->COMMANDE_FOURNDET_DONOTSEARCH_ANYWHERE)?'%':'';
			// Can use index if PRODUCT_DONOTSEARCH_ANYWHERE is on
			// For natural search
			$scrit = explode(' ', $filterkey);
			$i=0;
			if (count($scrit) > 1) $sql.="(";
			foreach ($scrit as $crit)
			{
				if ($i > 0) $sql.=" AND ";
				$sql.="(t.ref LIKE '".$db->escape($prefix.$crit)."%' OR t.description LIKE '".$db->escape($prefix.$crit)."%' OR p.ref LIKE '".$db->escape($prefix.$crit)."%' OR p.description LIKE '".$db->escape($prefix.$crit)."%' OR p.label LIKE '".$db->escape($prefix.$crit)."%'";
				$sql.=")";
				$i++;
			}
			if (count($scrit) > 1) $sql.=")";
			$sql.=')';
		}
		$sql.= $db->order("p.ref");
		$sql.= $db->plimit($limit);

		// Build output string
		dol_syslog(get_class($this)."::select_sol_almacendet_list_v search items", LOG_DEBUG);
		$result=$this->db->query($sql);
		if ($result)
		{
			//require_once DOL_DOCUMENT_ROOT.'/budget/class/items.class.php';
			$num = $this->db->num_rows($result);

			$out.='<select class="flat" name="'.$htmlname.'" id="'.$htmlname.'">';
			$out.='<option value="0" selected>&nbsp;</option>';

			$i = 0;
			while ($num && $i < $num)
			{
				$opt = '';
				$optJson = array();
				$objp = $this->db->fetch_object($result);
				$lView = true;
				$nBalance = $objp->qty_livree;
				$nSalida = 0;
				//si condicion $balance == 1 se debe revisar saldos
				if ($balance)
				{
					$filterstatic = " AND t.fk_product = ".$objp->fk_product;
					$filterstatic.= " AND t.origintype = 'solalmacendet'";
					$filterstatic.= " AND t.fk_origin = ".$objp->rowid;
					$filterstatic.= " AND t.type_mouvement = 1";
					$resstock = $objstock->fetchAll('','',0,0,array(1=>1),'AND', $filterstatic);
					if ($resstock>0)
					{
						foreach ($objstock->lines AS $j => $row)
						{
							//sumamos las salidas
							$nSalida+= $row->value;
						}
						$nBalance+=$nSalida;
						if ($nBalance <= 0)
							$lView = false;
					}
				}

				if ($lView)
				{
					$this->constructSolAlmacenListOption($objp, $opt, $optJson, $selected);
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
	 * constructCommandeFournListOption
	 *
	 * @param 	resultset	$objp			    Resultset of fetch
	 * @param 	string		$opt			    Option (var used for returned value in string option format)
	 * @param 	string		$optJson		    Option (var used for returned value in json format)
	 * @param 	int			$price_level	    Price level
	 * @param 	string		$selected		    Preselected value
	 * @param   int         $hidepriceinlabel   Hide price in label
	 * @return	void
	 */
	private function constructSolAlmacenListOption(&$objp, &$opt, &$optJson, $selected, $hidepriceinlabel=0)
	{
		global $langs,$conf,$user,$db;

		$outkey='';
		$outval='';
		$outref='';
		$outlabel='';
		$outdesc='';
		$outbarcode='';
		$outtype='';
		$outprice_ht='';
		$outprice_ttc='';
		$outpricebasetype='';
		$outtva_tx='';
		$outqty=1;
		$outdiscount=0;

		$maxlengtharticle=(empty($conf->global->ALMACEN_DET_MAX_LENGTH_COMBO)?48:$conf->global->ALMACEN_DET_MAX_LENGTH_COMBO);

		$label=$objp->label;
		if (! empty($objp->label_translated)) $label=$objp->label_translated;
		if (! empty($filterkey) && $filterkey != '') $label=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$label,1);

		$outkey=$objp->rowid;
		$outref=$objp->ref;
		$outlabel=$objp->label;
		$outdesc=$objp->detail;
		$opt = '<option value="'.$objp->rowid.'"';
		$opt.= ($objp->rowid == $selected)?' selected':'';
		$opt.= '>';
		$opt.= $objp->reff;
		$opt.= ' - '.$objp->ref;
		$opt.= ' - '.dol_trunc($label,$maxlengtharticle).' - ';
		//$opt.=' - '.dol_trunc($outdesc,$maxlengtharticle).' - ';

		$objRef = $objp->ref;
		if (! empty($filterkey) && $filterkey != '') $objRef=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$objRef,1);
		$outval.= $objp->reff;
		$outval.= ' - '.$objp->ref;
		$outval.= ' - '.dol_trunc($label,$maxlengtharticle).' - ';
		//$outval.=' - '.dol_trunc($outdesc,$maxlengtharticle).' - ';

		$found=0;
		$opt.= "</option>\n";
		$optJson = array('key'=>$outkey, 'value'=>$outref, 'label'=>$outval, 'label2'=>$outlabel, 'desc'=>$outdesc);
	}

	//select contrat members
	/**
	 *  Return list of products for commande fournisseur in Ajax if Ajax activated or go to select_produits_list
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
	function select_contrat_member_v($selected='', $htmlname='fk', $filtertype='', $limit=20, $status=1, $selected_input_value='', $hidelabel=0, $ajaxoptions=array(),$fk=0,$action='',$showempty=0)
	{
		global $langs,$conf;

		$price_level = (! empty($price_level) ? $price_level : 0);

		if (! empty($conf->use_javascript_ajax) && ! empty($conf->global->SALARY_CONTRAT_USE_SEARCH_TO_SELECT))
		{
			$placeholder='';

			if ($selected && empty($selected_input_value))
			{
				require_once DOL_DOCUMENT_ROOT.'/salary/class/pcontrat.class.php';
				$pcontrat 	= new Pcontrat($this->db);
				$pcontrat->fetch($selected);
				$selected_input_value=$pcontrat->ref;
			}
			// mode=1 means customers products
			$urloption='htmlname='.$htmlname.'&outjson=1&type='.$filtertype.'&status='.$status.'&selected='.$selected.'&fk_member='.$fk.'&id='.$selected;

			print ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/salary/ajax/pcontrat.php', $urloption, $conf->global->SALARY_CONTRAT_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
			if (empty($hidelabel)) print $langs->trans("RefOrLabel").' : ';
				else if ($hidelabel > 1) {
					if (! empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $placeholder=' placeholder="'.$langs->trans("RefOrLabel").'"';
					else $placeholder=' title="'.$langs->trans("RefOrLabel").'"';
					if ($hidelabel == 2) {
						print img_picto($langs->trans("Search"), 'search');
					}
				}
				print '<input type="text" class="theight20" size="10" name="search_'.$htmlname.'" id="search_'.$htmlname.'" value="'.$selected_input_value.'"'.$placeholder.'/>';

				if ($hidelabel == 3) {
					print img_picto($langs->trans("Search"), 'search');
				}
			}
			else
			{
				print $this->select_contrat_member_list_v($selected,$htmlname,$filtertype,$limit,'',$status,0,$fk,$showempty);
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
	 *  @param      int		$socid     		Thirdparty Id (to get also price dedicated to this customer)
	 *  @return     array    				Array of keys for json
	 */
	function select_contrat_member_list_v($selected='',$htmlname='fk',$filtertype='',$limit=20,$filterkey='',$status=1,$outputmode=0,$fk=0,$showempty=0)
	{
		global $langs,$conf,$user,$db;

		$out='';
		$outarray=array();

		$sql = "SELECT ";
		$sql.= " d.rowid, d.fk_user, d.date_ini, d.ref ";
		$sql.= " , t.lastname, t.firstname, t.login ";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_contract as d";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."adherent as t ON d.fk_user = t.rowid";
		$sql.= ' WHERE t.entity IN ('.getEntity('adherent', 1).')';
		if ($fk>0) $sql.= " AND d.fk_user = ".$fk;
		$sql.= " AND t.statut = 1 ";
		$sql.= " AND d.state = 1 ";
		// Add criteria on ref/label
		if ($filterkey != '')
		{
			$sql.=' AND (';
			$prefix=empty($conf->global->SALARY_CONTRAT_DONOTSEARCH_ANYWHERE)?'%':'';
			// Can use index if PRODUCT_DONOTSEARCH_ANYWHERE is on
			// For natural search
			$scrit = explode(' ', $filterkey);
			$i=0;
			if (count($scrit) > 1) $sql.="(";
			foreach ($scrit as $crit)
			{
				if ($i > 0) $sql.=" AND ";
				$sql.="(d.ref LIKE '".$db->escape($prefix.$crit)."%' OR t.lastname LIKE '".$db->escape($prefix.$crit)."%' OR t.firstname LIKE '".$db->escape($prefix.$crit)."%' OR t.login LIKE '".$db->escape($prefix.$crit)."%' OR t.email LIKE '".$db->escape($prefix.$crit)."%'";
				$sql.=")";
				$i++;
			}
			if (count($scrit) > 1) $sql.=")";
			$sql.=')';
		}
		$sql.= $db->order("d.ref");
		$sql.= $db->plimit($limit);

		// Build output string
		dol_syslog(get_class($this)."::select_contrat_member_list_v search items", LOG_DEBUG);
		$result=$this->db->query($sql);
		if ($result)
		{
			//require_once DOL_DOCUMENT_ROOT.'/budget/class/items.class.php';
			$num = $this->db->num_rows($result);

			$out.='<select class="flat" name="'.$htmlname.'" id="'.$htmlname.'">';
			if ($showempty)
				$out.='<option value="0" selected>&nbsp;</option>';

			$i = 0;
			while ($num && $i < $num)
			{
				$opt = '';
				$optJson = array();
				$objp = $this->db->fetch_object($result);
				$lView = true;

				if ($lView)
				{
					$this->constructContratMemberListOption($objp, $opt, $optJson, $selected);
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
	 * constructContratMemberListOption
	 *
	 * @param 	resultset	$objp			    Resultset of fetch
	 * @param 	string		$opt			    Option (var used for returned value in string option format)
	 * @param 	string		$optJson		    Option (var used for returned value in json format)
	 * @param 	int			$price_level	    Price level
	 * @param 	string		$selected		    Preselected value
	 * @param   int         $hidepriceinlabel   Hide price in label
	 * @return	void
	 */
	private function constructContratMemberListOption(&$objp, &$opt, &$optJson, $selected, $hidepriceinlabel=0)
	{
		global $langs,$conf,$user,$db;

		$outkey='';
		$outval='';
		$outref='';
		$outlabel='';
		$outdesc='';
		$outbarcode='';
		$outtype='';
		$outprice_ht='';
		$outprice_ttc='';
		$outpricebasetype='';
		$outtva_tx='';
		$outqty=1;
		$outdiscount=0;

		$maxlengtharticle=(empty($conf->global->CONTRAT_MEMBER_MAX_LENGTH_COMBO)?48:$conf->global->CONTRAT_MEMBER_MAX_LENGTH_COMBO);

		$label=$objp->lastname.' '.$objp->firstname;
		if (! empty($objp->label_translated)) $label=$objp->label_translated;
		if (! empty($filterkey) && $filterkey != '') $label=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$label,1);

		$outkey=$objp->rowid;
		$outref=$objp->ref;
		$outlabel=$objp->lastname.' '.$objp->firstname;
		$outdesc=$objp->detail;
		$opt = '<option value="'.$objp->rowid.'"';
		$opt.= ($objp->rowid == $selected)?' selected':'';
		$opt.= '>';
		$opt.= $objp->ref;
		$opt.= ' - '.dol_trunc($label,$maxlengtharticle).' - ';
		//$opt.=' - '.dol_trunc($outdesc,$maxlengtharticle).' - ';

		$objRef = $objp->ref;
		if (! empty($filterkey) && $filterkey != '') $objRef=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$objRef,1);
		$outval.= $objRref;
		$outval.= ' - '.dol_trunc($label,$maxlengtharticle).' - ';
		//$outval.=' - '.dol_trunc($outdesc,$maxlengtharticle).' - ';

		$found=0;
		$opt.= "</option>\n";
		$optJson = array('key'=>$outkey, 'value'=>$outref, 'label'=>$outval, 'label2'=>$outlabel, 'desc'=>$outdesc);
	}

	/**
	 *	Show a HTML widget to input a date or combo list for day, month, years and optionaly hours and minutes.
	 *  Fields are preselected with :
	 *            	- set_time date (must be a local PHP server timestamp or string date with format 'YYYY-MM-DD' or 'YYYY-MM-DD HH:MM')
	 *            	- local date in user area, if set_time is '' (so if set_time is '', output may differs when done from two different location)
	 *            	- Empty (fields empty), if set_time is -1 (in this case, parameter empty must also have value 1)
	 *
	 *	@param	timestamp	$set_time 		Pre-selected date (must be a local PHP server timestamp), -1 to keep date not preselected, '' to use current date (emptydate must be 0).
	 *	@param	string		$prefix			Prefix for fields name
	 *	@param	int			$h				1=Show also hours
	 *	@param	int			$m				1=Show also minutes
	 *	@param	int			$empty			0=Fields required, 1=Empty inputs are allowed, 2=Empty inputs are allowed for hours only
	 *	@param	string		$form_name 		Not used
	 *	@param	int			$d				1=Show days, month, years
	 * 	@param	int			$addnowlink		Add a link "Now"
	 * 	@param	int			$nooutput		Do not output html string but return it
	 * 	@param 	int			$disabled		Disable input fields
	 *  @param  int			$fullday        When a checkbox with this html name is on, hour and day are set with 00:00 or 23:59
	 *  @param	string		$addplusone		Add a link "+1 hour". Value must be name of another select_date field.
	 *  @param  datetime    $adddateof      Add a link "Date of invoice" using the following date.
	 * 	@return	string|null						Nothing or string if nooutput is 1
	 *  @see	form_date
	 */
	function select_date_v($set_time='', $prefix='re', $h=0, $m=0, $empty=0, $form_name="", $d=1, $addnowlink=0, $nooutput=0, $disabled=0, $fullday='', $addplusone='', $adddateof='',$k)
	{
		global $conf,$langs;

		$retstring='';

		if($prefix=='') $prefix='re';
		if($h == '') $h=0;
		if($m == '') $m=0;
		$emptydate=0;
		$emptyhours=0;
		if ($empty == 1) { $emptydate=1; $emptyhours=1; }
		if ($empty == 2) { $emptydate=0; $emptyhours=1; }
		$orig_set_time=$set_time;

		if ($set_time === '' && $emptydate == 0)
		{
			include_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
			$set_time = dol_now('tzuser')-(getServerTimeZoneInt('now')*3600); // set_time must be relative to PHP server timezone
		}

		// Analysis of the pre-selection date
		if (preg_match('/^([0-9]+)\-([0-9]+)\-([0-9]+)\s?([0-9]+)?:?([0-9]+)?/',$set_time,$reg))
			{
			// Date format 'YYYY-MM-DD' or 'YYYY-MM-DD HH:MM:SS'
				$syear	= (! empty($reg[1])?$reg[1]:'');
				$smonth	= (! empty($reg[2])?$reg[2]:'');
				$sday	= (! empty($reg[3])?$reg[3]:'');
				$shour	= (! empty($reg[4])?$reg[4]:'');
				$smin	= (! empty($reg[5])?$reg[5]:'');
			}
			elseif (strval($set_time) != '' && $set_time != -1)
			{
			// set_time est un timestamps (0 possible)
				$syear = dol_print_date($set_time, "%Y");
				$smonth = dol_print_date($set_time, "%m");
				$sday = dol_print_date($set_time, "%d");
				if ($orig_set_time != '')
				{
					$shour = dol_print_date($set_time, "%H");
					$smin = dol_print_date($set_time, "%M");
				}
			}
			else
			{
			// Date est '' ou vaut -1
				$syear = '';
				$smonth = '';
				$sday = '';
				$shour = !isset($conf->global->MAIN_DEFAULT_DATE_HOUR) ? '' : $conf->global->MAIN_DEFAULT_DATE_HOUR;
				$smin = !isset($conf->global->MAIN_DEFAULT_DATE_MIN) ? '' : $conf->global->MAIN_DEFAULT_DATE_MIN;
			}

			$usecalendar='combo';
			if (! empty($conf->use_javascript_ajax) && (empty($conf->global->MAIN_POPUP_CALENDAR) || $conf->global->MAIN_POPUP_CALENDAR != "none")) $usecalendar=empty($conf->global->MAIN_POPUP_CALENDAR)?'eldy':$conf->global->MAIN_POPUP_CALENDAR;
				if ($conf->browser->phone) $usecalendar='combo';

				if ($d)
				{
			// Show date with popup
					if ($usecalendar != 'combo')
					{
						$formated_date='';
				//print "e".$set_time." t ".$conf->format_date_short;
						if (strval($set_time) != '' && $set_time != -1)
						{
					//$formated_date=dol_print_date($set_time,$conf->format_date_short);
					$formated_date=dol_print_date($set_time,$langs->trans("FormatDateShortInput"));  // FormatDateShortInput for dol_print_date / FormatDateShortJavaInput that is same for javascript
				}

				// Calendrier popup version eldy
				if ($usecalendar == "eldy")
				{
					// Zone de saisie manuelle de la date
					$retstring.='<input id="'.$prefix.$k.'" name="'.$prefix.'[]" type="text" size="9" maxlength="11" value="'.$formated_date.'"';
					$retstring.=($disabled?' disabled':'');
					$retstring.=' onChange="dpChangeDay(\''.$prefix.'\',\''.$langs->trans("FormatDateShortJavaInput").'\'); "';  // FormatDateShortInput for dol_print_date / FormatDateShortJavaInput that is same for javascript
					$retstring.='>';

					// Icone calendrier
					if (! $disabled)
					{
						$retstring.='<button id="'.$prefix.$k.'Button[]" type="button" class="dpInvisibleButtons"';
						$base=DOL_URL_ROOT.'/core/';
						$retstring.=' onClick="showDP(\''.$base.'\',\''.$prefix.'\',\''.$langs->trans("FormatDateShortJavaInput").'\',\''.$langs->defaultlang.'\');">'.img_object($langs->trans("SelectDate"),'calendarday','class="datecallink"').'</button>';
					}
					else $retstring.='<button id="'.$prefix.$k.'Button" type="button" class="dpInvisibleButtons">'.img_object($langs->trans("Disabled"),'calendarday','class="datecallink"').'</button>';

					$retstring.='<input type="hidden" id="'.$prefix.'day[]"   name="'.$prefix.'day[]"   value="'.$sday.'">'."\n";
					$retstring.='<input type="hidden" id="'.$prefix.'month[]" name="'.$prefix.'month[]" value="'.$smonth.'">'."\n";
					$retstring.='<input type="hidden" id="'.$prefix.'year[]"  name="'.$prefix.'year[]"  value="'.$syear.'">'."\n";
				}
				else
				{
					print "Bad value of MAIN_POPUP_CALENDAR";
				}
			}
			// Show date with combo selects
			else
			{
				//$retstring.='<div class="inline-block">';
				// Day
				$retstring.='<select'.($disabled?' disabled':'').' class="flat" id="'.$prefix.$k.'day" name="'.$prefix.'day[]">';

				if ($emptydate || $set_time == -1)
				{
					$retstring.='<option value="0" selected>&nbsp;</option>';
				}

				for ($day = 1 ; $day <= 31; $day++)
				{
					$retstring.='<option value="'.$day.'"'.($day == $sday ? ' selected':'').'>'.$day.'</option>';
				}

				$retstring.="</select>";

				$retstring.='<select'.($disabled?' disabled':'').' class="flat" id="'.$prefix.'month" name="'.$prefix.$k.'month[]">';
				if ($emptydate || $set_time == -1)
				{
					$retstring.='<option value="0" selected>&nbsp;</option>';
				}

				// Month
				for ($month = 1 ; $month <= 12 ; $month++)
				{
					$retstring.='<option value="'.$month.'"'.($month == $smonth?' selected':'').'>';
					$retstring.=dol_print_date(mktime(12,0,0,$month,1,2000),"%b");
					$retstring.="</option>";
				}
				$retstring.="</select>";

				// Year
				if ($emptydate || $set_time == -1)
				{
					$retstring.='<input'.($disabled?' disabled':'').' placeholder="'.dol_escape_htmltag($langs->trans("Year")).'" class="flat" type="text" size="3" maxlength="4" id="'.$prefix.$k.'year[]" name="'.$prefix.'year[]" value="'.$syear.'">';
				}
				else
				{
					$retstring.='<select'.($disabled?' disabled':'').' class="flat" id="'.$prefix.$k.'year[]" name="'.$prefix.'year[]">';

					for ($year = $syear - 5; $year < $syear + 10 ; $year++)
					{
						$retstring.='<option value="'.$year.'"'.($year == $syear ? ' selected':'').'>'.$year.'</option>';
					}
					$retstring.="</select>\n";
				}
				//$retstring.='</div>';
			}
		}

		if ($d && $h) $retstring.='&nbsp;';

		if ($h)
		{
			// Show hour
			$retstring.='<select'.($disabled?' disabled':'').' class="flat '.($fullday?$fullday.'hour':'').'" id="'.$prefix.$k.'hour[]" name="'.$prefix.'hour[]">';
			if ($emptyhours) $retstring.='<option value="-1">&nbsp;</option>';
			for ($hour = 0; $hour < 24; $hour++)
			{
				if (strlen($hour) < 2) $hour = "0" . $hour;
				$retstring.='<option value="'.$hour.'"'.(($hour == $shour)?' selected':'').'>'.$hour.(empty($conf->dol_optimize_smallscreen)?'':'H').'</option>';
			}
			$retstring.='</select>';
			if ($m && empty($conf->dol_optimize_smallscreen)) $retstring.=":";
			}

			if ($m)
			{
			// Show minutes
				$retstring.='<select'.($disabled?' disabled':'').' class="flat '.($fullday?$fullday.'min':'').'" id="'.$prefix.$k.'min[]" name="'.$prefix.'min[]">';
				if ($emptyhours) $retstring.='<option value="-1">&nbsp;</option>';
				for ($min = 0; $min < 60 ; $min++)
				{
					if (strlen($min) < 2) $min = "0" . $min;
					$retstring.='<option value="'.$min.'"'.(($min == $smin)?' selected':'').'>'.$min.(empty($conf->dol_optimize_smallscreen)?'':'').'</option>';
				}
				$retstring.='</select>';
			}

		// Add a "Now" link
			if ($conf->use_javascript_ajax && $addnowlink)
			{
			// Script which will be inserted in the onClick of the "Now" link
				$reset_scripts = "";

			// Generate the date part, depending on the use or not of the javascript calendar
				$reset_scripts .= 'jQuery(\'#'.$prefix.$k.'\').val(\''.dol_print_date(dol_now(),'day').'\');';
				$reset_scripts .= 'jQuery(\'#'.$prefix.$k.'day[]\').val(\''.dol_print_date(dol_now(),'%d').'\');';
				$reset_scripts .= 'jQuery(\'#'.$prefix.$k.'month[]\').val(\''.dol_print_date(dol_now(),'%m').'\');';
				$reset_scripts .= 'jQuery(\'#'.$prefix.$k.'year[]\').val(\''.dol_print_date(dol_now(),'%Y').'\');';
			/*if ($usecalendar == "eldy")
			{
				$base=DOL_URL_ROOT.'/core/';
				$reset_scripts .= 'resetDP(\''.$base.'\',\''.$prefix.'\',\''.$langs->trans("FormatDateShortJavaInput").'\',\''.$langs->defaultlang.'\');';
			}
			else
			{
				$reset_scripts .= 'this.form.elements[\''.$prefix.'day\'].value=formatDate(new Date(), \'d\'); ';
				$reset_scripts .= 'this.form.elements[\''.$prefix.'month\'].value=formatDate(new Date(), \'M\'); ';
				$reset_scripts .= 'this.form.elements[\''.$prefix.'year\'].value=formatDate(new Date(), \'yyyy\'); ';
			}*/
			// Update the hour part
			if ($h)
			{
				if ($fullday) $reset_scripts .= " if (jQuery('#fullday:checked').val() == null) {";
				//$reset_scripts .= 'this.form.elements[\''.$prefix.'hour\'].value=formatDate(new Date(), \'HH\'); ';
					$reset_scripts .= 'jQuery(\'#'.$prefix.$k.'hour\').val(\''.dol_print_date(dol_now(),'%H').'\');';
					if ($fullday) $reset_scripts .= ' } ';
				}
			// Update the minute part
				if ($m)
				{
					if ($fullday) $reset_scripts .= " if (jQuery('#fullday:checked').val() == null) {";
				//$reset_scripts .= 'this.form.elements[\''.$prefix.'min\'].value=formatDate(new Date(), \'mm\'); ';
						$reset_scripts .= 'jQuery(\'#'.$prefix.$k.'min\').val(\''.dol_print_date(dol_now(),'%M').'\');';
						if ($fullday) $reset_scripts .= ' } ';
					}
			// If reset_scripts is not empty, print the link with the reset_scripts in the onClick
					if ($reset_scripts && empty($conf->dol_optimize_smallscreen))
					{
						$retstring.=' <button class="dpInvisibleButtons datenowlink" id="'.$prefix.'ButtonNow" type="button" name="_useless" value="now" onClick="'.$reset_scripts.'">';
						$retstring.=$langs->trans("Now");
						$retstring.='</button> ';
					}
				}

		// Add a "Plus one hour" link
				if ($conf->use_javascript_ajax && $addplusone)
				{
			// Script which will be inserted in the onClick of the "Add plusone" link
					$reset_scripts = "";

			// Generate the date part, depending on the use or not of the javascript calendar
					$reset_scripts .= 'jQuery(\'#'.$prefix.$k.'\').val(\''.dol_print_date(dol_now(),'day').'\');';
					$reset_scripts .= 'jQuery(\'#'.$prefix.$k.'day[]\').val(\''.dol_print_date(dol_now(),'%d').'\');';
					$reset_scripts .= 'jQuery(\'#'.$prefix.$k.'month[]\').val(\''.dol_print_date(dol_now(),'%m').'\');';
					$reset_scripts .= 'jQuery(\'#'.$prefix.$k.'year[]\').val(\''.dol_print_date(dol_now(),'%Y').'\');';
			// Update the hour part
					if ($h)
					{
						if ($fullday) $reset_scripts .= " if (jQuery('#fullday:checked').val() == null) {";
							$reset_scripts .= 'jQuery(\'#'.$prefix.$k.'hour\').val(\''.dol_print_date(dol_now(),'%H').'\');';
							if ($fullday) $reset_scripts .= ' } ';
						}
			// Update the minute part
						if ($m)
						{
							if ($fullday) $reset_scripts .= " if (jQuery('#fullday:checked').val() == null) {";
								$reset_scripts .= 'jQuery(\'#'.$prefix.$k.'min\').val(\''.dol_print_date(dol_now(),'%M').'\');';
								if ($fullday) $reset_scripts .= ' } ';
							}
			// If reset_scripts is not empty, print the link with the reset_scripts in the onClick
							if ($reset_scripts && empty($conf->dol_optimize_smallscreen))
							{
								$retstring.=' <button class="dpInvisibleButtons datenowlink" id="'.$prefix.'ButtonPlusOne" type="button" name="_useless2" value="plusone" onClick="'.$reset_scripts.'">';
								$retstring.=$langs->trans("DateStartPlusOne");
								$retstring.='</button> ';
							}
						}

		// Add a "Plus one hour" link
						if ($conf->use_javascript_ajax && $adddateof)
						{
							$tmparray=dol_getdate($adddateof);
							$retstring.=' - <button class="dpInvisibleButtons datenowlink" id="dateofinvoice" type="button" name="_dateofinvoice" value="now" onclick="jQuery(\'#re\').val(\''.dol_print_date($adddateof,'day').'\');jQuery(\'#reday[]\').val(\''.$tmparray['mday'].'\');jQuery(\'#remonth[]\').val(\''.$tmparray['mon'].'\');jQuery(\'#reyear[]\').val(\''.$tmparray['year'].'\');">'.$langs->trans("DateInvoice").'</a>';
						}

						if (! empty($nooutput)) return $retstring;

						print $retstring;
						return;
					}

	//select assets
	/**
	 *  Return list of assets for customer in Ajax if Ajax activated or go to select_asset_list_v
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
	function select_asset($selected='', $htmlname='productid', $filtertype='', $limit=20, $price_level=0, $status=1, $finished=2, $selected_input_value='', $hidelabel=0, $ajaxoptions=array(),$socid=0,$action='',$filter='',$fk_projet=0)
	{
		global $langs,$conf;
		$price_level = (! empty($price_level) ? $price_level : 0);

		if (! empty($conf->use_javascript_ajax) && ! empty($conf->global->ASSETS_USE_SEARCH_TO_SELECT))
		{
			$placeholder='';

			if ($selected && empty($selected_input_value))
			{
				require_once DOL_DOCUMENT_ROOT.'/assets/class/assets.class.php';
				$product = new Assets($this->db);
				$product->fetch($selected);
				$selected_input_value=$product->ref;
			}
			// mode=1 means customers products
			$urloption='htmlname='.$htmlname.'&outjson=1&price_level='.$price_level.'&type='.$filtertype.'&mode=1&status='.$status.'&finished='.$finished.'&selected='.$selected.'&filter='.$filter;
			$urloption.='&fk_projet='.$fk_projet;
			//Price by customer
			if (! empty($conf->global->PRODUIT_CUSTOMER_PRICES) && !empty($socid)) {
				$urloption.='&socid='.$socid;
			}
			print ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/assets/ajax/asset.php', $urloption, $conf->global->ASSETS_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
			if (empty($hidelabel)) print $langs->trans("RefOrLabel").' : ';
				else if ($hidelabel > 1) {
					if (! empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $placeholder=' placeholder="'.$langs->trans("RefOrLabel").'"';
					else $placeholder=' title="'.$langs->trans("RefOrLabel").'"';
					if ($hidelabel == 2) {
						print img_picto($langs->trans("Search"), 'search');
					}
				}
				print '<input type="text" class="theight20" size="10" name="search_'.$htmlname.'" id="search_'.$htmlname.'" value="'.$selected_input_value.'"'.$placeholder.' '.($_SESSION['rf'] != 1?($action=='addpay'?'':'autofocus="autofocus"'):'').'/>';

				if ($hidelabel == 3) {
					print img_picto($langs->trans("Search"), 'search');
				}
			}
			else
			{
				print $this->select_asset_list_v($selected,$htmlname,$filtertype,$limit,$price_level,'',$status,$finished,0,$socid,$filter,$fk_projet);
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
	 *  @param      int		$socid     		Thirdparty Id (to get also price dedicated to this customer)
	 *  @return     array    				Array of keys for json
	 */
	function select_asset_list_v($selected='',$htmlname='itemid',$filtertype='',$limit=20,$price_level=0,$filterkey='',$status='1,9',$finished=2,$outputmode=0,$socid=0,$filter='',$fk_projet=0)
	{
		global $langs,$conf,$user;

		require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentdetext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
		if ($conf->projet->enabled)
			require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
		//si $fk_projet> 0 solo listamos los activos asignados a ese proyecto
		$objassigndet = new Assetsassignmentdetext($this->db);
		$objassign    = new Assetsassignmentext($this->db);
		$out='';
		$outarray=array();

		$sql = "SELECT ";
		$sql.= " p.rowid, p.descrip AS detail, p.descrip AS label, p.ref";

		$sql.= " FROM ".MAIN_DB_PREFIX."assets as p";
		$sql.= ' WHERE p.entity IN ('.getEntity('assets', 1).')';
		if ($fk_projet)
		{
			$sql.= " AND p.statut IN (2,3)";
		}
		elseif ($status) $sql.= " AND p.statut IN (".$status.")";
		// Add criteria on ref/label
		if ($filterkey != '')
		{
			$sql.=' AND (';
			$prefix=empty($conf->global->ASSETS_DONOTSEARCH_ANYWHERE)?'%':'';
			// Can use index if PRODUCT_DONOTSEARCH_ANYWHERE is on
			// For natural search
			$scrit = explode(' ', $filterkey);
			$i=0;
			if (count($scrit) > 1) $sql.="(";
			foreach ($scrit as $crit)
			{
				if ($i > 0) $sql.=" AND ";
				$sql.="(p.ref LIKE '".$this->db->escape($prefix.$crit)."%' OR p.descrip LIKE '".$this->db->escape($prefix.$crit)."%'";
				$sql.=")";
				$i++;
			}
			if (count($scrit) > 1) $sql.=")";
			$sql.=')';
		}
		$sql.= $this->db->order("p.ref");
		$sql.= $this->db->plimit($limit);

		// Build output string
		dol_syslog(get_class($this)."::select_asset_list_v search items", LOG_DEBUG);
		$result=$this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);

			$out.='<select class="flat" name="'.$htmlname.'" id="'.$htmlname.'">';
			$out.='<option value="0" selected>&nbsp;</option>';

			$i = 0;
			while ($num && $i < $num)
			{
				$opt = '';
				$optJson = array();
				$objp = $this->db->fetch_object($result);
				$lAdd = true;
				$resa = $objassigndet->fetch_ult($objp->rowid,'1,2,3',1);
				if ($resa>0)
				{
					$objassign->fetch($objassigndet->fk_asset_assignment);
					if ($objassign->fk_projet && $conf->projet->enabled)
					{
						$projet = new Project($this->db);
						$projet->fetch($objassign->fk_projet);
						$objp->location = $projet->title;
						if ($fk_projet>0)
						{
							if ($objassign->fk_projet != $fk_projet)
								$lAdd = false;
						}
					}
					if (!$fk_projet)
					{
						if ($objassign->fk_property)
						{
							$mproperty = new Mproperty($this->db);
							$mproperty->fetch($objassign->fk_property);
							$objp->location.= $mproperty->ref;
						}
						if ($status == 1)
						{
							if ($objassign->fk_user != $finished) $lAdd = false;
						}
					}
				}
				else
				{
					if ($fk_projet>0)
						$lAdd = false;
				}

				if ($lAdd)
				{
					$this->constructAssetListOption($objp, $opt, $optJson, $price_level, $selected);
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
			dol_print_error($this->db);
		}
	}

	/**
	 * constructProductListOption
	 *
	 * @param 	resultset	$objp			    Resultset of fetch
	 * @param 	string		$opt			    Option (var used for returned value in string option format)
	 * @param 	string		$optJson		    Option (var used for returned value in json format)
	 * @param 	int			$price_level	    Price level
	 * @param 	string		$selected		    Preselected value
	 * @param   int         $hidepriceinlabel   Hide price in label
	 * @return	void
	 */
	private function constructAssetListOption(&$objp, &$opt, &$optJson, $price_level, $selected, $hidepriceinlabel=0)
	{
		global $langs,$conf,$user;

		$outkey='';
		$outval='';
		$outref='';
		$outlabel='';
		$outdesc='';
		$outbarcode='';
		$outtype='';
		$outprice_ht='';
		$outprice_ttc='';
		$outpricebasetype='';
		$outtva_tx='';
		$outqty=1;
		$outdiscount=0;

		$maxlengtharticle=(empty($conf->global->ASSETS_MAX_LENGTH_COMBO)?48:$conf->global->ASSETS_MAX_LENGTH_COMBO);

		$label=$objp->label;
		if (! empty($objp->label_translated)) $label=$objp->label_translated;
		if (! empty($filterkey) && $filterkey != '') $label=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$label,1);

		$outkey=$objp->rowid;
		$outref=$objp->ref;
		$outlabel=$objp->label;
		$outdesc=$objp->description;
		$outbarcode=$objp->barcode;

		$outtype=$objp->fk_product_type;
		$opt = '<option value="'.$objp->rowid.'"';
		$opt.= ($objp->rowid == $selected)?' selected':'';
		$opt.= '>';
		$opt.= $objp->ref;
		$opt.=' - '.dol_trunc($label,$maxlengtharticle).' ';
		//if ($objp->location) $opt.=' - '.' ('.$objp->location.')';

		$objRef = $objp->ref;
		if (! empty($filterkey) && $filterkey != '') $objRef=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$objRef,1);
		$outval.=$objRef;
		$outval.=' - '.dol_trunc($label,$maxlengtharticle).' ';
		//if ($objp->location) $outval.=' - '.' ('.$objp->location.')';
		$found=0;

		$opt.= "</option>\n";
		$optJson = array('key'=>$outkey, 'value'=>$outref, 'label'=>$outval, 'label2'=>$outlabel, 'desc'=>$outdesc, 'type'=>$outtype, 'price_ht'=>$outprice_ht, 'price_ttc'=>$outprice_ttc, 'pricebasetype'=>$outpricebasetype, 'tva_tx'=>$outtva_tx, 'qty'=>$outqty, 'discount'=>$outdiscount, 'duration_value'=>$outdurationvalue, 'duration_unit'=>$outdurationunit);
	}

	/**
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param	string	$selected       Id or Code or Label of preselected country
	 *  @param  string	$htmlname       Name of html select object
	 *  @param  string	$htmloption     Options html on select object
	 *  @param	string	$maxlength		Max length for labels (0=no limit)
	 *  @return string           		HTML string with select
	 */
	function select_departament($selected='',$htmlname='fk_father',$htmloption='',$maxlength=0,$showempty=0,$filter='',$display=0)
	{
		global $conf,$langs;

		$langs->load("orgman");

		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT rowid, ref as code_iso, label as label";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_departament";
		$sql.= " WHERE entity = ".$conf->entity;
		if ($filter) $sql.= $filter;
		$sql.= " ORDER BY ref ASC";

		dol_syslog(get_class($this)."::select_departament sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
			if ($showempty)
			{
				$out.= '<option value="-1"';
				if ($selected == -1) $out.= ' selected="selected"';
				$out.= '>&nbsp;</option>';
			}

			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				$foundselected=false;

				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$countryArray[$i]['rowid'] 		= $obj->rowid;
					$countryArray[$i]['code_iso'] 	= $obj->code_iso;
					$countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Departament".$obj->code_iso)!="Departament".$obj->code_iso?$langs->transnoentitiesnoconv("Departament".$obj->code_iso):($obj->label!='-'?$obj->label:''));
					$label[$i] 	= $countryArray[$i]['label'];
					$i++;
				}

				array_multisort($label, SORT_ASC, $countryArray);

				foreach ($countryArray as $row)
				{
					//print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
					if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['code_iso'] || $selected == $row['label']) )
					{
						$foundselected=true;
						$out.= '<option value="'.$row['rowid'].'" selected="selected">';
					}
					else
					{
						$out.= '<option value="'.$row['rowid'].'">';
					}
					if ($display == 1)
					{
						$out.= $row['code_iso'];
					}
					else
					{
						$out.= dol_trunc($row['label'],$maxlength,'middle');
						if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
					}
					$out.= '</option>';
				}
			}
			$out.= '</select>';
		}
		else
		{
			dol_print_error($this->db);
		}

		return $out;
	}

	//date 20170329
	/**
	 *  Return combo list of poa structure, into language of user
	 *
	 *  @param	string	$selected       Id or Code or Label of preselected country
	 *  @param  string	$htmlname       Name of html select object
	 *  @param  string	$htmloption     Options html on select object
	 *  @param	string	$maxlength	Max length for labels (0=no limit)
	 *  @param	string	$showempty	View space labels (0=no view)

	 *  @return string           		HTML string with select
	 */
	function select_structure($selected='',$htmlname='fk_father',$htmloption='',$maxlength=0,$showempty=0,$pos=3,$filter='')
	{
		global $conf,$langs;

		$langs->load("poa@poa");

		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT rowid, sigla as label, label as code_iso, fk_father";
		$sql.= " FROM ".MAIN_DB_PREFIX."poa_structure ";
		$sql.= " WHERE entity = ".$conf->entity;
		$sql.= " AND period_year = ".$_SESSION['period_year'];
		if ($filter) $sql.= $filter;
		if (!empty($pos))
			$sql.= " AND pos = ".$pos;
		$sql.= " ORDER BY sigla ASC";
		dol_syslog(get_class($this)."::select_structure sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$out.= '<select id="select'.$htmlname.'" class="form-control" name="'.$htmlname.'" '.$htmloption.'>';
			if ($showempty)
			{
				$out.= '<option value="-1"';
				if ($selected == -1) $out.= ' selected="selected"';
				$out.= '>&nbsp;</option>';
			}

			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				$foundselected=false;

				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$countryArray[$i]['rowid'] 		= $obj->rowid;
					$countryArray[$i]['code_iso'] 	= $obj->code_iso;

					$countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Structure".$obj->code_iso)!="Structure".$obj->code_iso?$langs->transnoentitiesnoconv("Structure".$obj->code_iso):($obj->label!='-'?$obj->label:''));
					$label[$i] 	= $countryArray[$i]['label'];
					$i++;
				}

				array_multisort($label, SORT_ASC, $countryArray);

				foreach ($countryArray as $row)
				{
					//print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
					if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['code_iso'] || $selected == $row['label']) )
					{
						$foundselected=true;
						$out.= '<option value="'.$row['rowid'].'" selected="selected">';
					}
					else
					{
						$out.= '<option value="'.$row['rowid'].'">';
					}
					//$out.= dol_trunc($row['label'],$maxlength,'middle');
					$out.= $row['label'];
					if ($row['code_iso']) $out.= ' ('.dol_trunc($row['code_iso'],$maxlength,'middle') . ')';
					$out.= '</option>';
				}
			}
			$out.= '</select>';
		}
		else
		{
			dol_print_error($this->db);
		}

		return $out;
	}

	/**
	 *    Return list of categories having choosed type
	 *
	 *    @param	int		$type				Type of category ('customer', 'supplier', 'contact', 'product', 'member'). Old mode (0, 1, 2, ...) is deprecated.
	 *    @param    string	$selected    		Id of category preselected or 'auto' (autoselect category if there is only one element)
	 *    @param    string	$htmlname			HTML field name
	 *    @param    int		$maxlength      	Maximum length for labels
	 *    @param    int		$excludeafterid 	Exclude all categories after this leaf in category tree.
	 *    @param	int		$outputmode			0=HTML select string, 1=Array
	 *    @return	string
	 *    @see select_categories
	 */
	function select_all_departaments($selected='', $htmlname="parent", $maxlength=64, $excludeafterid=0, $outputmode=0,$filter='')
	{

		global $langs;
		$langs->load("categories");

		include_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';

		$cat = new Pdepartamentext($this->db);
		$cate_arbo = $cat->get_full_arbo($excludeafterid, $filter);

		$output = '<select class="flat" name="'.$htmlname.'">';
		$outarray=array();
		if (is_array($cate_arbo))
		{
			if (! count($cate_arbo)) $output.= '<option value="-1" disabled>'.$langs->trans("NoCategoriesDefined").'</option>';
			else
			{
				$output.= '<option value="-1">&nbsp;</option>';
				foreach($cate_arbo as $key => $value)
				{
					if ($cate_arbo[$key]['id'] == $selected || ($selected == 'auto' && count($cate_arbo) == 1))
					{
						$add = 'selected ';
					}
					else
					{
						$add = '';
					}
					$output.= '<option '.$add.'value="'.$cate_arbo[$key]['id'].'">'.dol_trunc($cate_arbo[$key]['fulllabel'],$maxlength,'middle').'</option>';

					$outarray[$cate_arbo[$key]['id']] = $cate_arbo[$key]['fulllabel'];
				}
			}
		}
		$output.= '</select>';
		$output.= "\n";

		if ($outputmode) return $outarray;
		return $output;
	}

	//date 20170329
	/**
	 *  Return combo list of poa structure, into language of user
	 *
	 *  @param	string	$selected       Id or Code or Label of preselected country
	 *  @param  string	$htmlname       Name of html select object
	 *  @param  string	$htmloption     Options html on select object
	 *  @param	string	$maxlength	Max length for labels (0=no limit)
	 *  @param	string	$showempty	View space labels (0=no view)

	 *  @return string           		HTML string with select
	 */
	function select_founding_source($selected='',$htmlname='fk_source',$htmloption='',$maxlength=0,$showempty=0,$filter='')
	{
		global $conf,$langs;

		$langs->load("orgman@orgman");

		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT rowid, code as code_iso, label";
		$sql.= " FROM ".MAIN_DB_PREFIX."c_sources ";
		$sql.= " WHERE entity = ".$conf->entity;
		$sql.= " AND active = 1";
		if ($filter) $sql.= $filter;
		$sql.= " ORDER BY label ASC";
		dol_syslog(get_class($this)."::select_founding_source sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$out.= '<select id="select'.$htmlname.'" class="form-control" name="'.$htmlname.'" '.$htmloption.'>';
			if ($showempty)
			{
				$out.= '<option value="-1"';
				if ($selected == -1) $out.= ' selected="selected"';
				$out.= '>&nbsp;</option>';
			}

			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				$foundselected=false;

				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$countryArray[$i]['rowid'] 		= $obj->rowid;
					$countryArray[$i]['code_iso'] 	= $obj->code_iso;

					$countryArray[$i]['label']		= $obj->label;
					$label[$i] 	= $countryArray[$i]['label'];
					$i++;
				}

				array_multisort($label, SORT_ASC, $countryArray);

				foreach ($countryArray as $row)
				{
					//print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
					if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['code_iso'] || $selected == $row['label']) )
					{
						$foundselected=true;
						$out.= '<option value="'.$row['rowid'].'" selected="selected">';
					}
					else
					{
						$out.= '<option value="'.$row['rowid'].'">';
					}
					//$out.= dol_trunc($row['label'],$maxlength,'middle');
					$out.= $row['label'];
					if ($row['code_iso']) $out.= ' ('.dol_trunc($row['code_iso'],$maxlength,'middle') . ')';
					$out.= '</option>';
				}
			}
			$out.= '</select>';
		}
		else
		{
			dol_print_error($this->db);
		}

		return $out;
	}

	//date 20170329
	/**
	 *  Return combo list type mant
	 *
	 *  @param	string	$selected       Id or Code or Label of preselected country
	 *  @param  string	$htmlname       Name of html select object
	 *  @param  string	$htmloption     Options html on select object
	 *  @param	string	$maxlength	Max length for labels (0=no limit)
	 *  @param	string	$showempty	View space labels (0=no view)

	 *  @return string           		HTML string with select
	 */
	function select_type_repair($selected='',$htmlname='fk_type_repair',$htmloption='',$maxlength=0,$showempty=0,$filter='')
	{
		global $conf,$langs;

		$langs->load("mant");

		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT rowid, ref as code_iso, label";
		$sql.= " FROM ".MAIN_DB_PREFIX."m_type_repair ";
		$sql.= " WHERE entity = ".$conf->entity;
		$sql.= " AND active = 1";
		if ($filter) $sql.= $filter;
		$sql.= " ORDER BY label ASC";

		dol_syslog(get_class($this)."::select_type_mant sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$out.= '<select id="select'.$htmlname.'" class="form-control" name="'.$htmlname.'" '.$htmloption.'>';
			if ($showempty)
			{
				$out.= '<option value="-1"';
				if ($selected == -1) $out.= ' selected="selected"';
				$out.= '>&nbsp;</option>';
			}

			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				$foundselected=false;

				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$countryArray[$i]['rowid'] 		= $obj->rowid;
					$countryArray[$i]['code_iso'] 	= $obj->code_iso;

					$countryArray[$i]['label']		= $obj->label;
					$label[$i] 	= $countryArray[$i]['label'];
					$i++;
				}

				array_multisort($label, SORT_ASC, $countryArray);

				foreach ($countryArray as $row)
				{
					//print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
					if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['code_iso'] || $selected == $row['label']) )
					{
						$foundselected=true;
						$out.= '<option value="'.$row['rowid'].'" selected="selected">';
					}
					else
					{
						$out.= '<option value="'.$row['rowid'].'">';
					}
					//$out.= dol_trunc($row['label'],$maxlength,'middle');
					$out.= $row['label'];
					if ($row['code_iso']) $out.= ' ('.dol_trunc($row['code_iso'],$maxlength,'middle') . ')';
					$out.= '</option>';
				}
			}
			$out.= '</select>';
		}
		else
		{
			dol_print_error($this->db);
		}
		return $out;
	}

   	/**
	 *	Return list of types of lines (product or service)
	 * 	Example: 0=product, 1=service, 9=other (for external module)
	 *
	 *	@param  string	$selected       Preselected type
	 *	@param  string	$htmlname       Name of field in html form
	 * 	@param	int		$showempty		Add an empty field
	 * 	@param	int		$hidetext		Do not show label 'Type' before combo box (used only if there is at least 2 choices to select)
	 * 	@param	integer	$forceall		1=Force to show products and services in combo list, whatever are activated modules, 0=No force, -1=Force none (and set hidden field to 'service')
	 *  @return	void
	 */
   	function select_type_of_lines_add($selected='',$htmlname='type',$showempty=0,$hidetext=0,$forceall=0)
   	{
   		global $db,$langs,$user,$conf;

		// If product & services are enabled or both disabled.
   		if ($forceall > 0 || (empty($forceall) && ! empty($conf->product->enabled) && ! empty($conf->service->enabled)) || (empty($forceall) && empty($conf->product->enabled) && empty($conf->service->enabled)) )
   		{
   			if (empty($hidetext)) print $langs->trans("Type").': ';
   				print '<select class="flat" id="select_'.$htmlname.'" name="'.$htmlname.'">';
   				if ($showempty)
   				{
   					print '<option value="-1"';
   					if ($selected == -1) print ' selected';
   					print '>&nbsp;</option>';
   				}
   				if (!$conf->poa->enabled)
   				{
   					print '<option value="0"';
   					if (0 == $selected) print ' selected';
   					print '>'.$langs->trans("Product");
   				}
   				if ($conf->poa->enabled)
   				{
   					print '<option value="0"';
   					if (0 == $selected) print ' selected';
   					print '>'.$langs->trans("Assets").'</option>';
   				}
   				print '<option value="1"';
   				if (1 == $selected) print ' selected';
   				print '>'.$langs->trans("Service").'</option>';

   				print '</select>';
			//if ($user->admin) print info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
   			}
   			if (empty($forceall) && empty($conf->product->enabled) && ! empty($conf->service->enabled))
   			{
   				print $langs->trans("Service");
   				print '<input type="hidden" name="'.$htmlname.'" value="1">';
   			}
   			if (empty($forceall) && ! empty($conf->product->enabled) && empty($conf->service->enabled))
   			{
   				print $langs->trans("Product");
   				print '<input type="hidden" name="'.$htmlname.'" value="0">';
   			}
   			if ($forceall < 0)
		// This should happened only for contracts when both predefined product and service are disabled.
   			{
   				print '<input type="hidden" name="'.$htmlname.'" value="1">';
			// By default we set on service for contract. If CONTRACT_SUPPORT_PRODUCTS is set, forceall should be 1 not -1
   			}
   		}
	//charge select
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
	function select_charge_v($selected='', $htmlname='fk_charge', $limit=20, $status=1,$selected_input_value='', $hidelabel=0, $ajaxoptions=array(),$socid=0,$action='',$element='charge')
	{
		global $langs,$conf;

		$price_level = (! empty($price_level) ? $price_level : 0);

		if (! empty($conf->use_javascript_ajax) && ! empty($conf->global->CHARGE_USE_SEARCH_TO_SELECT))
		{
			$placeholder='';

			if ($selected && empty($selected_input_value))
			{
				require_once DOL_DOCUMENT_ROOT.'/orgman/class/pchargeext.class.php';
				$charge = new Pchargeext($this->db);
				$charge->fetch($selected);
				$selected_input_value=$charge->ref;
				$labelcharge = $charge->label;
			}
			// mode=1 means customers products
			$urloption='htmlname='.$htmlname.'&outjson=1&type='.$filtertype.'&mode=1&status='.$status.'&finished='.$finished;
			//se direcciona a un solo lugar
			print ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/orgman/ajax/charge.php', $urloption, $conf->global->CHARGE_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
			if (empty($hidelabel)) print $langs->trans("RefOrLabel").' : ';
				else if ($hidelabel > 1) {
					if (! empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $placeholder=' placeholder="'.$langs->trans("RefOrLabel").'"';
					else $placeholder=' title="'.$langs->trans("RefOrLabel").'"';
					if ($hidelabel == 2) {
						print img_picto($langs->trans("Search"), 'search');
					}
				}
				print '<input type="text" class="theight20" size="13" name="search_'.$htmlname.'" id="search_'.$htmlname.'" value="'.$selected_input_value.'"'.$placeholder.' onblur="revisaFrame(this.value);"  '.($_SESSION['rf'] != 1?($action=='addpay'?'':'autofocus="autofocus"'):'').'/>';

				if ($hidelabel == 3) {
					print img_picto($langs->trans("Search"), 'search');
				}
			}
			else
			{
				print $this->select_charge_list_v($selected,$htmlname,$limit,'',$status);
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
	function select_charge_list_v($selected='',$htmlname='fk_charge',$limit=20,$filterkey='',$status=1)
	{
		global $langs,$conf,$user,$db;

		$out='';
		$outarray=array();

		$sql = "SELECT ";
		$sql.= " p.rowid, p.label, p.ref, p.detail, p.active";

		$sql.= " FROM ".MAIN_DB_PREFIX."p_charge as p";
		$sql.= ' WHERE p.entity IN ('.getEntity('charge', 1).')';
		if ($active >0)
		{
			$sql.= " AND p.active = ".$active;
		}
		// Add criteria on ref/label
		if ($filterkey != '')
		{
			$sql.=' AND (';
			$prefix=empty($conf->global->CHARGE_DONOTSEARCH_ANYWHERE)?'%':'';	// Can use index if PRODUCT_DONOTSEARCH_ANYWHERE is on
			// For natural search
			$scrit = explode(' ', $filterkey);
			$i=0;
			if (count($scrit) > 1) $sql.="(";
			foreach ($scrit as $crit)
			{
				if ($i > 0) $sql.=" AND ";
				$sql.="(p.ref LIKE '".$prefix.$crit."%' OR p.label LIKE '".$prefix.$crit."%' OR p.detail LIKE '".$prefix.$crit."%'";
				$sql.=")";
				$i++;
			}
			if (count($scrit) > 1) $sql.=")";
			$sql.=')';
		}
		if ($filterstatic) $sql.= $filterstatic;
		$sql.= $db->order("p.ref");
		$sql.= $db->plimit($limit);

		// Build output string
		dol_syslog(get_class($this)."::select_charge_list_v search charge sql=".$sql, LOG_DEBUG);
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

				$this->constructChargeListOption_v($objp, $opt, $optJson, $price_level, $selected);
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
	 * @param 	resultset	$objp			Resultset of fetch
	 * @param 	string		$opt			Option
	 * @param 	string		$optJson		Option
	 * @param 	int			$price_level	Price level
	 * @param 	string		$selected		Preselected value
	 * @return	void
	 */
	private function constructChargeListOption_v(&$objp, &$opt, &$optJson, $price_level, $selected)
	{
		global $langs,$conf,$user,$db;

		$langs->load("budget");
		$langs->load("monprojet");
		$langs->load("product");
		$langs->load("others");

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
		$objDesc=$objp->description;
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
		//marcamos si biene de budget
		if ($objp->budget) $opt.= $objp->refbudget.' -> ';
		$opt.= $objp->ref.' - '.dol_trunc($label,32).' - ';
		$opt.= $objp->description;
		if ($objp->reel) $opt.= ' - '.$langs->trans('Balance').' '.$objp->reel;

		$objRef = $objp->ref;
		if (! empty($filterkey) && $filterkey != '') $objRef=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$objRef,1);
		if ($objp->budget) $outval.= $objp->refbudget.' -> ';
		$outval.=$objRef.' - '.dol_trunc($label,32).' - ';
		$outval.=$objDesc;
		if ($objp->reel) $outval.= ' - '.$langs->trans('Balance').' '.$objp->reel;

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
		if (!empty($conf->global->PRODUIT_CUSTOMER_PRICES))
		{
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

	//select equipment
	/**
	 *  Return list of assets for customer in Ajax if Ajax activated or go to select_asset_list_v
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
	function select_equipment($selected='', $htmlname='fk_equipment', $filtertype='', $limit=20, $price_level=0, $status='1,9', $finished=2, $selected_input_value='', $hidelabel=0, $ajaxoptions=array(),$socid=0,$action='',$filter='',$fk_projet=0)
	{
		global $langs,$conf;
		$price_level = (! empty($price_level) ? $price_level : 0);

		if (! empty($conf->use_javascript_ajax) && ! empty($conf->global->MANT_EQUIPMENT_USE_SEARCH_TO_SELECT))
		{
			$placeholder='';

			if ($selected && empty($selected_input_value))
			{
				require_once DOL_DOCUMENT_ROOT.'/mant/class/mequipment.class.php';
				$product = new Mequipment($this->db);
				$product->fetch($selected);
				$selected_input_value=$product->ref;
			}
			// mode=1 means customers products
			$urloption='htmlname='.$htmlname.'&outjson=1&price_level='.$price_level.'&type='.$filtertype.'&mode=1&status='.$status.'&finished='.$finished.'&selected='.$selected.'&filter='.$filter;
			$urloption.='&fk_projet='.$fk_projet;
			print ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/mant/ajax/equipment.php', $urloption, $conf->global->MANT_EQUIPMENT_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
			if (empty($hidelabel)) print $langs->trans("RefOrLabel").' : ';
				else if ($hidelabel > 1) {
					if (! empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $placeholder=' placeholder="'.$langs->trans("RefOrLabel").'"';
					else $placeholder=' title="'.$langs->trans("RefOrLabel").'"';
					if ($hidelabel == 2) {
						print img_picto($langs->trans("Search"), 'search');
					}
				}
				print '<input type="text" class="theight20" size="10" name="search_'.$htmlname.'" id="search_'.$htmlname.'" value="'.$selected_input_value.'"'.$placeholder.' '.($_SESSION['rf'] != 1?($action=='addpay'?'':'autofocus="autofocus"'):'').'/>';

				if ($hidelabel == 3) {
					print img_picto($langs->trans("Search"), 'search');
				}
			}
			else
			{
				print $this->select_equipment_list_v($selected,$htmlname,$filtertype,$limit,$price_level,'',$status,$finished,0,$socid,$filter,$fk_projet);
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
	 *  @param      int		$socid     		Thirdparty Id (to get also price dedicated to this customer)
	 *  @return     array    				Array of keys for json
	 */
	function select_equipment_list_v($selected='',$htmlname='itemid',$filtertype='',$limit=20,$price_level=0,$filterkey='',$status='1,9',$finished=2,$outputmode=0,$socid=0,$filter='',$fk_projet=0)
	{
		global $langs,$conf,$user;

		//require_once DOL_DOCUMENT_ROOT.'/mant/class/assetsassignmentdetext.class.php';
		//require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/orgman/class/mproperty.class.php';
		//si $fk_projet> 0 solo listamos los activos asignados a ese proyecto
		//$objassigndet = new Assetsassignmentdetext($this->db);
		//$objassign    = new Assetsassignmentext($this->db);
		$out='';
		$outarray=array();

		$sql = "SELECT ";
		$sql.= " p.rowid, p.label AS detail, p.label AS label, p.ref, p.ref_ext ";

		$sql.= " FROM ".MAIN_DB_PREFIX."m_equipment as p";
		$sql.= ' WHERE p.entity IN ('.getEntity('mant', 1).')';
		if ($status) $sql.= " AND p.status IN (".$status.")";
		// Add criteria on ref/label
		if ($filterkey != '')
		{
			$sql.=' AND (';
			$prefix=empty($conf->global->MANT_DONOTSEARCH_ANYWHERE)?'%':'';
			// Can use index if PRODUCT_DONOTSEARCH_ANYWHERE is on
			// For natural search
			$scrit = explode(' ', $filterkey);
			$i=0;
			if (count($scrit) > 1) $sql.="(";
			foreach ($scrit as $crit)
			{
				if ($i > 0) $sql.=" AND ";
				$sql.="(p.ref LIKE '".$this->db->escape($prefix.$crit)."%' OR p.label LIKE '".$this->db->escape($prefix.$crit)."%'";
				$sql.= " OR p.ref_ext LIKE '".$this->db->escape($prefix.$crit)."%'";
				$sql.=")";
				$i++;
			}
			if (count($scrit) > 1) $sql.=")";
			$sql.=')';
		}
		$sql.= $this->db->order("p.ref");
		$sql.= $this->db->plimit($limit);

		// Build output string
		dol_syslog(get_class($this)."::select_equipment_list_v search items", LOG_DEBUG);
		$result=$this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);

			$out.='<select class="flat" name="'.$htmlname.'" id="'.$htmlname.'">';
			$out.='<option value="0" selected>&nbsp;</option>';

			$i = 0;
			while ($num && $i < $num)
			{
				$opt = '';
				$optJson = array();
				$objp = $this->db->fetch_object($result);
				$lAdd = true;


				if ($lAdd)
				{
					$this->constructEquipmentListOption($objp, $opt, $optJson, $price_level, $selected);
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
			dol_print_error($this->db);
		}
	}

	/**
	 * constructEquipmentListOption
	 *
	 * @param 	resultset	$objp			    Resultset of fetch
	 * @param 	string		$opt			    Option (var used for returned value in string option format)
	 * @param 	string		$optJson		    Option (var used for returned value in json format)
	 * @param 	int			$price_level	    Price level
	 * @param 	string		$selected		    Preselected value
	 * @param   int         $hidepriceinlabel   Hide price in label
	 * @return	void
	 */
	private function constructEquipmentListOption(&$objp, &$opt, &$optJson, $price_level, $selected, $hidepriceinlabel=0)
	{
		global $langs,$conf,$user;

		$outkey='';
		$outval='';
		$outref='';
		$outref_ext = '';
		$outlabel='';
		$outdesc='';
		$outbarcode='';
		$outtype='';
		$outprice_ht='';
		$outprice_ttc='';
		$outpricebasetype='';
		$outtva_tx='';
		$outqty=1;
		$outdiscount=0;

		$maxlengtharticle=(empty($conf->global->MANT_MAX_LENGTH_COMBO)?48:$conf->global->MANT_MAX_LENGTH_COMBO);

		$label=$objp->label;
		if (! empty($objp->label_translated)) $label=$objp->label_translated;
		if (! empty($filterkey) && $filterkey != '') $label=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$label,1);

		$outkey=$objp->rowid;
		$outref=$objp->ref;
		$outref_ext = $objp->ref_ext;
		$outlabel=$objp->label;
		$outdesc=$objp->description;
		$outbarcode=$objp->barcode;

		$outtype=$objp->fk_product_type;
		$opt = '<option value="'.$objp->rowid.'"';
		$opt.= ($objp->rowid == $selected)?' selected':'';
		$opt.= '>';
		$opt.= $objp->ref;
		$opt.=' - '.dol_trunc($label,$maxlengtharticle).' ';
		//if ($objp->location) $opt.=' - '.' ('.$objp->location.')';

		$objRef = $objp->ref;
		if (! empty($filterkey) && $filterkey != '') $objRef=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$objRef,1);
		$outval.=$objRef;
		if (!empty($outref_ext))
			$outval.= ' - '.$outref_ext;
		$outval.=' - '.dol_trunc($label,$maxlengtharticle).' ';
		//if ($objp->location) $outval.=' - '.' ('.$objp->location.')';
		$found=0;

		$opt.= "</option>\n";
		$optJson = array('key'=>$outkey, 'value'=>$outref, 'label'=>$outval, 'label2'=>$outlabel, 'desc'=>$outdesc, 'type'=>$outtype, 'price_ht'=>$outprice_ht, 'price_ttc'=>$outprice_ttc, 'pricebasetype'=>$outpricebasetype, 'tva_tx'=>$outtva_tx, 'qty'=>$outqty, 'discount'=>$outdiscount, 'duration_value'=>$outdurationvalue, 'duration_unit'=>$outdurationunit);
	}

	/**
	 *    Return list of categories having choosed type
	 *
	 *    @param	int		$type				Type of category ('customer', 'supplier', 'contact', 'product', 'member'). Old mode (0, 1, 2, ...) is deprecated.
	 *    @param    string	$selected    		Id of category preselected or 'auto' (autoselect category if there is only one element)
	 *    @param    string	$htmlname			HTML field name
	 *    @param    int		$maxlength      	Maximum length for labels
	 *    @param    int		$excludeafterid 	Exclude all categories after this leaf in category tree.
	 *    @param	int		$outputmode			0=HTML select string, 1=Array
	 *    @return	string
	 *    @see select_categories
	 */
	function select_all_categories_salary($type, $selected='', $htmlname="parent", $maxlength=64, $excludeafterid=0, $outputmode=0)
	{
		global $langs;
		$langs->load("categories");

		include_once DOL_DOCUMENT_ROOT.'/salary/class/categorieext.class.php';

		// For backward compatibility
		if (is_numeric($type))
		{
			dol_syslog(__METHOD__ . ': using numeric value for parameter type is deprecated. Use string code instead.', LOG_WARNING);
		}

		$cat = new Categorieext($this->db);
		$cate_arbo = $cat->get_full_arboadd($type,$excludeafterid);

		$output = '<select class="flat" name="'.$htmlname.'">';
		$outarray=array();
		if (is_array($cate_arbo))
		{
			if (! count($cate_arbo)) $output.= '<option value="-1" disabled>'.$langs->trans("NoCategoriesDefined").'</option>';
			else
			{
				$output.= '<option value="-1">&nbsp;</option>';
				foreach($cate_arbo as $key => $value)
				{
					if ($cate_arbo[$key]['id'] == $selected || ($selected == 'auto' && count($cate_arbo) == 1))
					{
						$add = 'selected ';
					}
					else
					{
						$add = '';
					}
					$output.= '<option '.$add.'value="'.$cate_arbo[$key]['id'].'">'.dol_trunc($cate_arbo[$key]['fulllabel'],$maxlength,'middle').'</option>';

					$outarray[$cate_arbo[$key]['id']] = $cate_arbo[$key]['fulllabel'];
				}
			}
		}
		$output.= '</select>';
		$output.= "\n";

		if ($outputmode) return $outarray;
		return $output;
	}
}
?>