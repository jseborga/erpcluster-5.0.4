<?php
require_once DOL_DOCUMENT_ROOT.'/assets/assets/class/assets.class.php';

class Assetsext extends Assets
{
		//MODIFICADO
		
	var $db;
	var $error;
	var $num;

	// Cache arrays
	var $cache_types_paiements=array();
	var $cache_conditions_paiements=array();
	var $cache_availability=array();
	var $cache_demand_reason=array();
	var $cache_types_fees=array();
	var $cache_vatrates=array();

	var $tva_taux_value;
	var $tva_taux_libelle;


	/**
	 * Constructor
	 *
	 * @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}



	/**
	 *  Return list of products for customer in Ajax if Ajax activated or go to select_produits_list
	 *
	 *  @param		int			$selected				Preselected products
	 *  @param		string		$htmlname				Name of HTML seletc field (must be unique in page)
	 *  @param		int			$filtertype				Filter on product type (''=nofilter, 0=product, 1=service)
	 *  @param		int			$limit					Limit on number of returned lines
	 *  @param		int			$price_level			Level of price to show
	 *  @param		int			$status					-1=Return all products, 0=Products not on sell, 1=Products on sell
	 *  @param		int			$finished				2=all, 1=finished, 0=raw material
	 *  @param		string		$selected_input_value	Value of preselected input text (with ajax)
	 *  @param		int			$hidelabel				Hide label (0=no, 1=yes, 2=show search icon (before) and placeholder, 3 search icon after)
	 *  @param		array		$ajaxoptions			Options for ajax_autocompleter
	 *  @return		void
	 */
	function select_assets_line($selected='', $htmlname='fk_equipment', $filtertype='', $limit=20, $price_level=0, $status=1, $finished=2, $selected_input_value='', $hidelabel=0, $ajaxoptions=array(),$fk_projet=0)
	{
		global $langs,$conf;
		
		$price_level = (! empty($price_level) ? $price_level : 0);
		
		if (! empty($conf->use_javascript_ajax) && ! empty($conf->global->ASSETS_USE_SEARCH_TO_SELECT))
		{
			$placeholder='';	  
			if ($selected && empty($selected_input_value))
			{
				require_once DOL_DOCUMENT_ROOT.'/assets/assets/class/assets.class.php';
				$assets = new Assets($this->db);
				$assets->fetch($selected);
				$selected_input_value=$assets->ref;
			}
	  		// mode=1 means customers products
			$urloption='htmlname='.$htmlname.'&outjson=1&price_level='.$price_level.'&type='.$filtertype.'&mode=1&status='.$status.'&finished='.$finished;
			print ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/assets/ajax/assets.php', $urloption, $conf->global->ASSETS_USE_SEARCH_TO_SELECT, 0, $ajaxoptions);
			if (empty($hidelabel)) print $langs->trans("RefOrLabel").' : ';
			else if ($hidelabel > 1) {
				if (! empty($conf->global->MAIN_HTML5_PLACEHOLDER)) $placeholder=' placeholder="'.$langs->trans("RefOrLabel").'"';
				else $placeholder=' title="'.$langs->trans("RefOrLabel").'"';
				if ($hidelabel == 2) 
				{
					print img_picto($langs->trans("Search"), 'search');
				}
			}
			print '<input type="text" size="20" name="search_'.$htmlname.'" id="search_'.$htmlname.'" value="'.$selected_input_value.'"'.$placeholder.' />';
			if ($hidelabel == 3) 
			{
				print img_picto($langs->trans("Search"), 'search');
			}
		}
		else
		{
			print $this->select_assets_list($selected,$htmlname,$filtertype,$limit,$price_level,'',$status,$finished,0,$fk_projet);
		}
	}

	/**
	 *	Return list of products for a customer
	 *
	 *	@param      int		$selected       Preselected product
	 *	@param      string	$htmlname       Name of select html
	 *  @param		string	$filtertype     Filter on product type (''=nofilter, 0=product, 1=service)
	 *	@param      int		$limit          Limite sur le nombre de lignes retournees
	 *	@param      int		$price_level    Level of price to show
	 * 	@param      string	$filterkey      Filter on product
	 *	@param		int		$status         -1=Return all products, 0=Products not on sell, 1=Products on sell
	 *  @param      int		$finished       Filter on finished field: 2=No filter
	 *  @param      int		$outputmode     0=HTML select string, 1=Array
	 *  @return     array    				Array of keys for json
	 */
	function select_assets_list($selected='',$htmlname='fk_equipment',$filtertype='',$limit=20,$price_level=0,$filterkey='',$status=1,$finished=2,$outputmode=0,$fk_projet=0)
	{
		global $langs,$conf,$user,$db;
		
		$out='';
		$outarray=array();
		
		if ($fk_projet)
		{
			$sql = "SELECT ";
			$sql.= " d.rowid, d.fk_asset, p.descrip, p.ref, p.descrip AS description, p.descrip as label";

			$sql.= " FROM ".MAIN_DB_PREFIX."assets_assignment as a";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."assets_assignment_det AS d ON d.fk_asset_assignment = a.rowid";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."assets AS p ON d.fk_asset = p.rowid";
			$sql.= ' WHERE a.fk_projet IN ('.$fk_projet.')';
			$sql.= " AND a.statut = 2";
			$sql.= " AND d.statut = 1";
			$sql.= $db->order("p.ref");
			$sql.= $db->plimit($limit);
		}
		else
		{
			$sql = "SELECT ";
			$sql.= " p.rowid, p.rowid as fk_asset, p.descrip, p.ref, p.descrip AS description, p.descrip as label";
			$sql.= " FROM ".MAIN_DB_PREFIX."assets as p";
			$sql.= ' WHERE p.entity IN ('.getEntity('assets', 1).')';
			$sql.= $db->order("p.ref");
			$sql.= $db->plimit($limit);
		}
	  // Build output string
		dol_syslog(get_class($this)."::select_assets_list search assets sql=".$sql, LOG_DEBUG);
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
				$this->constructAssetsListOption($objp, $opt, $optJson, $price_level, $selected);
				$out.=$opt;
				array_push($outarray, $optJson);
				
				$i++;
			}
			
			$out.='</select>';
			
			$this->db->free($result);
	  //echo '<hr>'.$outputmode;
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
	 * @param 	resultset	&$objp			Resultset of fetch
	 * @param 	string		&$opt			Option
	 * @param 	string		&$optJson		Option
	 * @param 	int			$price_level	Price level
	 * @param 	string		$selected		Preselected value
	 * @return	void
	 */
	private function constructAssetsListOption(&$objp, &$opt, &$optJson, $price_level, $selected)
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
	  // $opt.= (!empty($objp->price_by_qty_rowid) && $objp->price_by_qty_rowid > 0)?' pbq="'.$objp->price_by_qty_rowid.'"':'';
	  // if (! empty($conf->stock->enabled) && $objp->fk_product_type == 0 && isset($objp->stock))
	  //   {
	  // 	  if ($objp->stock > 0) $opt.= ' class="product_line_stock_ok"';
	  // 	  else if ($objp->stock <= 0) $opt.= ' class="product_line_stock_too_low"';
	  //   }
		$opt.= '>';
		$opt.= $objp->ref.' - '.dol_trunc($objp->label,32).' - ';
		
		$objRef = $objp->ref;
		if (! empty($filterkey) && $filterkey != '') $objRef=preg_replace('/('.preg_quote($filterkey).')/i','<strong>$1</strong>',$objRef,1);
		$outval.=$objRef.' - '.dol_trunc($label,32).' - ';
		
		$found=0;
		
		
		
		
	  // if (! empty($conf->stock->enabled) && isset($objp->stock) && $objp->fk_product_type == 0)
	  //   {
	  // 	  $opt.= ' - '.$langs->trans("Stock").':'.$objp->stock;
	  // 	  $outval.=' - '.$langs->transnoentities("Stock").':'.$objp->stock;
	  //   }
		
	  // if ($objp->duration)
	  //   {
	  // 	  $duration_value = substr($objp->duration,0,dol_strlen($objp->duration)-1);
	  // 	  $duration_unit = substr($objp->duration,-1);
	  // 	  if ($duration_value > 1)
	  //       {
	  // 	      $dur=array("h"=>$langs->trans("Hours"),"d"=>$langs->trans("Days"),"w"=>$langs->trans("Weeks"),"m"=>$langs->trans("Months"),"y"=>$langs->trans("Years"));
	  //       }
	  // 	  else
	  //       {
	  // 	      $dur=array("h"=>$langs->trans("Hour"),"d"=>$langs->trans("Day"),"w"=>$langs->trans("Week"),"m"=>$langs->trans("Month"),"y"=>$langs->trans("Year"));
	  //       }
	  // 	  $opt.= ' - '.$duration_value.' '.$langs->trans($dur[$duration_unit]);
	  // 	  $outval.=' - '.$duration_value.' '.$langs->transnoentities($dur[$duration_unit]);
	  //   }
		
		$opt.= "</option>\n";
		$optJson = array('key'=>$outkey, 'value'=>$outref, 'label'=>$outval, 'label2'=>$outlabel, 'desc'=>$outdesc, 'type'=>$outtype, 'price_ht'=>$outprice_ht, 'price_ttc'=>$outprice_ttc, 'pricebasetype'=>$outpricebasetype, 'tva_tx'=>$outtva_tx, 'qty'=>$outqty, 'discount'=>$outdiscount);
		
	}
			
	/**
	 *	Returns the text label from units dictionary
	 *
	 * 	@param	string $type Label type (long or short)
	 *	@return	string|int <0 if ko, label if ok
	 */
	public function getLabelOfUnit($type='long')
	{
		global $langs;

		if (!$this->fk_unit) {
			return '';
		}

		$langs->load('products');

		$this->db->begin();

		$label_type = 'label';

		if ($type == 'short')
		{
			$label_type = 'short_label';
		}

		$sql = 'select '.$label_type.' from '.MAIN_DB_PREFIX.'c_units where rowid='.$this->fk_unit;
		$resql = $this->db->query($sql);
		if($resql && $this->db->num_rows($resql) > 0)
		{
			$res = $this->db->fetch_array($resql);
			$label = $langs->trans($res[$label_type]);
			$this->db->free($resql);
			return $label;
		}
		else
		{
			$this->error=$this->db->error().' sql='.$sql;
			dol_syslog(get_class($this)."::getLabelOfUnit Error ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function fetch_max($type)
	{
		global $langs,$conf;
		$sql = "SELECT";
		$sql.= " MAX(t.item_asset) AS item_asset";

		$sql.= " FROM ".MAIN_DB_PREFIX."assets as t";
		$sql.= " WHERE t.entity = ".$conf->entity;
		$sql.= " AND t.type_group = '".$type."'";

		dol_syslog(get_class($this)."::fetch_max sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->maximo = 1;
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);
				$this->maximo = $obj->item_asset + 1;
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_max ".$this->error, LOG_ERR);
			return -1;
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
		$langs->load("assets@assets");

		$dir = DOL_DOCUMENT_ROOT . "/assets/core/modules";

		if (! empty($conf->global->ASSETS_ADDON))
		{
			$file = $conf->global->ASSETS_ADDON.".php";
			// Chargement de la classe de numerotation
			$classname = $conf->global->ASSETS_ADDON;
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
					dol_print_error($db,"Assets::getNextNumRef ".$obj->error);
					return "";
				}
			}
			else
			{
				print $langs->trans("Error")." ".$langs->trans("Error_ASSETS_ADDON_NotDefined");
				return "";
			}
		}
		else
		{
			print $langs->trans("Error")." ".$langs->trans("Error_ASSETS_ADDON_NotDefined");
			return "";
		}
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
	function select_assets($selected='',$htmlname='fk_asset',$htmloption='',$maxlength=0,$showempty=0,$idnot=0,$required='',$exclude='',$include='',$mark='')
	{
		global $conf,$langs;

		$langs->load("mant@mant");
		if ($required)
			$required = 'required="required"';
		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT c.rowid, c.ref as code_iso, c.descrip as label";
		$sql.= " FROM ".MAIN_DB_PREFIX."assets AS c ";
		$sql.= " WHERE c.entity = ".$conf->entity;
		$sql.= " AND c.statut = 9";
		if ($idnot) $sql.= " AND c.rowid NOT IN (".$idnot.")";
		//if ($mark) $sql.= " AND (c.mark iS NULL OR c.mark = '' OR c.mark = ' ')";
		$sql.= " ORDER BY c.ref ASC";
		//echo $sql;
		dol_syslog(get_class($this)."::select_assets sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$out.= '<select id="select'.$htmlname.'" class="flat selectpays" '.$required.' name="'.$htmlname.'" '.$htmloption.'>';
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
					if (empty($exclude[$obj->rowid]))
					{
						if (empty($include))
						{
							$countryArray[$i]['rowid'] 		= $obj->rowid;
							$countryArray[$i]['code_iso'] 	= $obj->code_iso;
							$countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Area".$obj->code_iso)!="Area".$obj->code_iso?$langs->transnoentitiesnoconv("Area".$obj->code_iso):($obj->label!='-'?$obj->label:''));
							$label[$i] 	= $countryArray[$i]['label'];
						}
						elseif($include[$obj->rowid])
						{
							$countryArray[$i]['rowid'] 		= $obj->rowid;
							$countryArray[$i]['code_iso'] 	= $obj->code_iso;
							$countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Area".$obj->code_iso)!="Area".$obj->code_iso?$langs->transnoentitiesnoconv("Area".$obj->code_iso):($obj->label!='-'?$obj->label:''));
							$label[$i] 	= $countryArray[$i]['label'];
						}
					}
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
					$out.= dol_trunc($row['label'],$maxlength,'middle');
					if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
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



	function calc_cost_unit()
	{
		global $langs;
		$amount = $this->coste;
		//si existe mas variables dejamos abierto para sumar
		$total = $amount;

		//dividimos entre las unidades de vida util
		//$pu = price2num($total / $this->useful_life);
		//debe tener un costo unitario del activo segun la unidad de uso
		$pu = $this->coste_unit_use;
		return $pu;
	}


	//function para obtener la ultima asignacion activa
	function fetch_location($status=1,$limit=1)
	{
		global $conf,$langs;
		require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentext.class.php';
		require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentdetext.class.php';
		$objdet = new Assetsassignmentdetext($this->db);
		$obj = new Assetsassignmentext($this->db);
		$objdet->fetch_ult($this->id,$status,$limit);
		if ($objdet->fk_asset_assignment)
		{
			$res = $obj->fetch($objdet->fk_asset_assignment);
			if ($res >0)
			{
				if ($obj->fk_projet)
				{
					if ($conf->monprojet->enabled)
					{
						require_once DOL_DOCUMENT_ROOT.'/monprojet/class/projectext.class.php';
						$projet = new Projectext($this->db);
					}
					else
					{
						require_once DOL_DOCUMENT_ROOT.'/projet/class/project.class.php';
						$projet = new Project($this->db);
					}
					if ($projet->fetch($obj->fk_projet))
					{
						$array = array('id'=>$projet->id,'ref'=>$projet->ref,'label'=>$projet->label,'link'=>($conf->monprojet->enabled?$projet->getNomUrladd(1):$projet->getNomUrl(1)));
					}
				}
				if ($obj->fk_property)
				{
					require_once DOL_DOCUMENT_ROOT.'/assets/property/class/mproperty.class.php';
					require_once DOL_DOCUMENT_ROOT.'/assets/property/class/mlocation.class.php';
					$property = new Mproperty($this->db);
					$location = new Mlocation($this->db);
					if ($property->fetch($obj->fk_property))
					{
						$location->fetch($obj->fk_location);
						$array = array('id'=>$property->id,'ref'=>$property->ref,'label'=>$location->detail,'link'=>$property->getNomUrl(1));
					}
				}
				return $array;
			}
		}
		return array();
	}	

	//fetch_lines
	public function fetch_lines()
	{
		global $langs,$conf;
		require_once DOL_DOCUMENT_ROOT.'/assets/class/assetsmovext.class.php';
		$assetsmov = new Assetsmovext($this->db);
		$filterstatic = " AND t.fk_asset = ".$this->id;
		$res = $assetsmov->fetchAll('ASC', 'ref',0,0,array(1=>1),'AND',$filterstatic);
		if ($res>0)
		{
			$this->lines = $assetsmov->lines;
			return $res;
		}
		return $res;
	}
}
?>