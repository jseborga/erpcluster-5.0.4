<?php
/* Copyright (c) 2002-2007 Rodolphe Quiedeville  <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur   <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Benoit Mortier        <benoit.mortier@opensides.be>
 * Copyright (C) 2004      Sebastien Di Cintio   <sdicintio@ressource-toi.org>
 * Copyright (C) 2004      Eric Seigne           <eric.seigne@ryxeo.com>
 * Copyright (C) 2005-2013 Regis Houssin         <regis.houssin@capnetworks.com>
 * Copyright (C) 2006      Andre Cianfarani      <acianfa@free.fr>
 * Copyright (C) 2006      Marc Barilley/Ocebo   <marc@ocebo.com>
 * Copyright (C) 2007      Franky Van Liedekerke <franky.van.liedekerker@telenet.be>
 * Copyright (C) 2007      Patrick Raguin        <patrick.raguin@gmail.com>
 * Copyright (C) 2010      Juanjo Menent         <jmenent@2byte.es>
 * Copyright (C) 2010      Philippe Grand        <philippe.grand@atoo-net.com>
 * Copyright (C) 2011      Herve Prot            <herve.prot@symeos.com>
 * Copyright (C) 2012      Marcos García         <marcosgdf@gmail.com>
 * Copyright (C) 2013      Raphaël Doursenaud   <rdoursenaud@gpcsolutions.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/core/class/html.form.class.php
 *  \ingroup    core
 *	\brief      File of class with all html predefined components
 */


/**
 *	Class to manage generation of HTML components
 *	Only common components must be here.
 */
class form_assets extends Assets
{
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
	function select_assets_line($selected='', $htmlname='fk_equipment', $filtertype='', $limit=20, $price_level=0, $status=1, $finished=2, $selected_input_value='', $hidelabel=0, $ajaxoptions=array())
	{
		global $langs,$conf;

		$price_level = (! empty($price_level) ? $price_level : 0);

		if (! empty($conf->use_javascript_ajax) && ! empty($conf->global->ASSETS_USE_SEARCH_TO_SELECT))
		{
			$placeholder='';
			if ($selected && empty($selected_input_value))
			{
				require_once DOL_DOCUMENT_ROOT.'/assets/class/assets.class.php';
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
			print $this->select_assets_list($selected,$htmlname,$filtertype,$limit,$price_level,'',$status,$finished,0);
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
	function select_assets_list($selected='',$htmlname='fk_equipment',$filtertype='',$limit=20,$price_level=0,$filterkey='',$status=1,$finished=2,$outputmode=0)
	{
		global $langs,$conf,$user,$db;

		$out='';
		$outarray=array();

		$sql = "SELECT ";
		$sql.= " p.rowid, p.descrip, p.ref, p.descrip AS description";
		$sql.= " FROM ".MAIN_DB_PREFIX."assets as p";
		$sql.= ' WHERE p.entity IN ('.getEntity('assets', 1).')';
		$sql.= $db->order("p.ref");
		$sql.= $db->plimit($limit);

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

}

?>
