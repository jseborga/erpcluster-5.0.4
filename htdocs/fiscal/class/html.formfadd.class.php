<?php
require_once DOL_DOCUMENT_ROOT.'/core/class/html.form.class.php';

class Formfadd extends Form
{
	var $cache_type_facture;
	var $type_facture_id;
	var $type_facture_code;
	var $type_facture_label;
	var $cache_type_tva;
	var $type_tva_id;
	var $type_tva_code;
	var $type_tva_label;

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

	function load_type_tvay($htmlname='type_tva', $selectedrate='', $societe_vendeuse='', $societe_acheteuse='', $idprod=0, $info_bits=0, $type='', $options_only=false)
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
					if ($rate['type'] != $filter) $lView = false;
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
		global $langs;

		$num = count($this->cache_type_facture);
		if ($num > 0) return $num;    // Cache deja charge

		$sql  = "SELECT DISTINCT t.rowid, t.code, t.label, t.detail, t.type";
		$sql.= " FROM ".MAIN_DB_PREFIX."c_type_facture as t ";
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
					$this->cache_type_facture[$i]['rowid']  = $obj->rowid;
					$this->cache_type_facture[$i]['code'] = $obj->code;
					$this->cache_type_facture[$i]['label'] = $obj->label;
					$this->cache_type_facture[$i]['detail'] = $obj->detail;
					$this->cache_type_facture[$i]['type'] = $obj->type;
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
		$txid=array();
		$txcode=array();
		$txlabel=array();
		$txdetail=array();


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
		global $langs;

		$num = count($this->cache_type_tva);
		if ($num > 0) return $num;    
		// Cache deja charge

		$sql  = "SELECT DISTINCT t.rowid, t.code, t.label";
		$sql.= " FROM ".MAIN_DB_PREFIX."c_type_tva as t ";
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
}
?>