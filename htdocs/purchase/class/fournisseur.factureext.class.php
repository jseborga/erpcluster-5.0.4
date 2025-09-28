<?php
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
/* This is to show add lines */

class FactureFournisseurext extends FactureFournisseur
{

	var $aArray;
    /**
     *	Return clicable name (with picto eventually)
     *
     *	@param		int		$withpicto		0=No picto, 1=Include picto into link, 2=Only picto
     *	@param		string	$option			Sur quoi pointe le lien
     * 	@param		int		$max			Max length of shown ref
     * 	@return		string					Chaine avec URL
     */
    function getNomUrladd($withpicto=0,$option='',$max=0,$target=0,$newclass='')
    {
        global $langs;

        $result='';
        $label = '<u>' . $langs->trans("ShowSupplierInvoice") . '</u>';
        if (! empty($this->ref))
            $label .= '<br><b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;
        if (! empty($this->ref_ext))
            $label .= '<br><b>' . $langs->trans('Invoice') . ':</b> ' . $this->ref_ext;
        if (! empty($this->ref_supplier))
            $label.= '<br><b>' . $langs->trans('RefSupplier') . ':</b> ' . $this->ref_supplier;
        if (! empty($this->total_ht))
            $label.= '<br><b>' . $langs->trans('AmountHT') . ':</b> ' . price($this->total_ht, 0, $langs, 0, -1, -1, $conf->currency);
        if (! empty($this->total_tva))
            $label.= '<br><b>' . $langs->trans('VAT') . ':</b> ' . price($this->total_tva, 0, $langs, 0, -1, -1, $conf->currency);
        if (! empty($this->total_ttc))
            $label.= '<br><b>' . $langs->trans('AmountTTC') . ':</b> ' . price($this->total_ttc, 0, $langs, 0, -1, -1, $conf->currency);

        if ($option == 'document')
        {
            $link = '<a href="'.DOL_URL_ROOT.'/purchase/facture/document.php?facid='.$this->id.'" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip '.$newclass.'" '.($target?' target="_blank"':'').'>';
            $linkend='</a>';
        }
        else
        {
            $link = '<a href="'.DOL_URL_ROOT.'/purchase/facture/card.php?facid='.$this->id.'" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip '.$newclass.'" '.($target?' target="_blank"':'').'>';
            $linkend='</a>';
        }

        $ref=$this->ref;
        $ref = $this->ref_ext;
        if (empty($ref)) $ref=$this->id;

        if ($withpicto) $result.=($link.img_object($label, 'bill', 'class="classfortooltip"').$linkend.' ');
        $result.=$link.($max?dol_trunc($ref,$max):$ref).$linkend;
        return $result;
    }

	//registro del enlace de factura y origen
	function addobject_linked()
	{
		// Add object linked
		if ($this->id && ! empty($this->origin) && ! empty($this->origin_id))
		{
			$ret = $this->add_object_linked();
			if (! $ret)
			{
				dol_print_error($this->db);
				$error++;
			}
		}
		if (!$error)
			return 1;
		else
			return -1;
	}

	//ACTUALIZATOTALES
	function updatetot($user=null, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;

		// Clean parameters
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->ref_supplier)) $this->ref_supplier=trim($this->ref_supplier);
		if (isset($this->entity)) $this->entity=trim($this->entity);
		if (isset($this->type)) $this->type=trim($this->type);
		if (isset($this->fk_soc)) $this->fk_soc=trim($this->fk_soc);
		if (isset($this->libelle)) $this->libelle=trim($this->libelle);
		if (isset($this->paye)) $this->paye=trim($this->paye);
		if (isset($this->amount)) $this->amount=trim($this->amount);
		if (isset($this->remise)) $this->remise=trim($this->remise);
		if (isset($this->close_code)) $this->close_code=trim($this->close_code);
		if (isset($this->close_note)) $this->close_note=trim($this->close_note);
		if (isset($this->tva)) $this->tva=trim($this->tva);
		if (isset($this->localtax1)) $this->localtax1=trim($this->localtax1);
		if (isset($this->localtax2)) $this->localtax2=trim($this->localtax2);
		if (empty($this->total)) $this->total=0;
		if (empty($this->total_ht)) $this->total_ht=0;
		if (empty($this->total_tva)) $this->total_tva=0;
		//	if (isset($this->total_localtax1)) $this->total_localtax1=trim($this->total_localtax1);
		//	if (isset($this->total_localtax2)) $this->total_localtax2=trim($this->total_localtax2);
		if (isset($this->total_ttc)) $this->total_ttc=trim($this->total_ttc);
		if (isset($this->statut)) $this->statut=trim($this->statut);
		if (isset($this->author)) $this->author=trim($this->author);
		if (isset($this->fk_user_valid)) $this->fk_user_valid=trim($this->fk_user_valid);
		if (isset($this->fk_facture_source)) $this->fk_facture_source=trim($this->fk_facture_source);
		if (isset($this->fk_project)) $this->fk_project=trim($this->fk_project);
		if (isset($this->cond_reglement_id)) $this->cond_reglement_id=trim($this->cond_reglement_id);
		if (isset($this->note_private)) $this->note=trim($this->note_private);
		if (isset($this->note_public)) $this->note_public=trim($this->note_public);
		if (isset($this->model_pdf)) $this->model_pdf=trim($this->model_pdf);
		if (isset($this->import_key)) $this->import_key=trim($this->import_key);


		// Check parameters
		// Put here code to add control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."facture_fourn SET";
		$sql.= " paye=".(isset($this->paye)?$this->paye:"null").",";
		$sql.= " amount=".(isset($this->amount)?$this->amount:"null").",";
		$sql.= " remise=".(isset($this->remise)?$this->remise:"null").",";
		//$sql.= " close_code=".(isset($this->close_code)?"'".$this->db->escape($this->close_code)."'":"null").",";
		//$sql.= " close_note=".(isset($this->close_note)?"'".$this->db->escape($this->close_note)."'":"null").",";
		$sql.= " tva=".(isset($this->tva)?$this->tva:"null").",";
		$sql.= " localtax1=".(isset($this->localtax1)?$this->localtax1:"null").",";
		$sql.= " localtax2=".(isset($this->localtax2)?$this->localtax2:"null").",";
		$sql.= " total=".(isset($this->total)?$this->total:"null").",";
		$sql.= " total_ht=".(isset($this->total_ht)?$this->total_ht:"null").",";
		$sql.= " total_tva=".(isset($this->total_tva)?$this->total_tva:"null").",";
		$sql.= " total_ttc=".(isset($this->total_ttc)?$this->total_ttc:"null")."";
		//$sql.= " fk_statut=".(isset($this->statut)?$this->statut:"null").",";
		//$sql.= " fk_user_author=".(isset($this->author)?$this->author:"null").",";
		//$sql.= " fk_user_valid=".(isset($this->fk_user_valid)?$this->fk_user_valid:"null").",";
		//$sql.= " fk_facture_source=".(isset($this->fk_facture_source)?$this->fk_facture_source:"null").",";
		//$sql.= " fk_projet=".(isset($this->fk_project)?$this->fk_project:"null").",";
		//$sql.= " fk_cond_reglement=".(isset($this->cond_reglement_id)?$this->cond_reglement_id:"null").",";
		//$sql.= " date_lim_reglement=".(dol_strlen($this->date_echeance)!=0 ? "'".$this->db->idate($this->date_echeance)."'" : 'null').",";
		//$sql.= " note_private=".(isset($this->note_private)?"'".$this->db->escape($this->note_private)."'":"null").",";
		//$sql.= " note_public=".(isset($this->note_public)?"'".$this->db->escape($this->note_public)."'":"null").",";
		//$sql.= " model_pdf=".(isset($this->model_pdf)?"'".$this->db->escape($this->model_pdf)."'":"null").",";
		//$sql.= " import_key=".(isset($this->import_key)?"'".$this->db->escape($this->import_key)."'":"null")."";
		$sql.= " WHERE rowid=".$this->id;

		$this->db->begin();

		dol_syslog(get_class($this)."::updatetot", LOG_DEBUG);
		$resql = $this->db->query($sql);

		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			if (! $notrigger)
			{
				// Call trigger
				//$result=$this->call_trigger('BILL_SUPPLIER_UPDATE',$user);
				//if ($result < 0) $error++;
				// End call triggers
			}
		}

		// Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
				dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
				$this->error.=($this->error?', '.$errmsg:$errmsg);
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
	  $dirtpls=array_merge($conf->modules_parts['tpl'],array('core/tpl2'));
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
	 *	Ajoute une ligne de facture (associe a aucun produit/service predefini)
	 *	Les parametres sont deja cense etre juste et avec valeurs finales a l'appel
	 *	de cette methode. Aussi, pour le taux tva, il doit deja avoir ete defini
	 *	par l'appelant par la methode get_default_tva(societe_vendeuse,societe_acheteuse,idprod)
	 *	et le desc doit deja avoir la bonne valeur (a l'appelant de gerer le multilangue).
	 *
	 *	@param    	string	$desc            	Description de la ligne
	 *	@param    	double	$pu              	Prix unitaire (HT ou TTC selon price_base_type, > 0 even for credit note)
	 *	@param    	double	$txtva           	Taux de tva force, sinon -1
	 *	@param		double	$txlocaltax1		LocalTax1 Rate
	 *	@param		double	$txlocaltax2		LocalTax2 Rate
	 *	@param    	double	$qty             	Quantite
	 *	@param    	int		$fk_product      	Id du produit/service predefini
	 *	@param    	double	$remise_percent  	Pourcentage de remise de la ligne
	 *	@param    	date	$date_start      	Date de debut de validite du service
	 * 	@param    	date	$date_end        	Date de fin de validite du service
	 * 	@param    	string	$ventil          	Code de ventilation comptable
	 *	@param    	int		$info_bits			Bits de type de lines
	 *	@param    	string	$price_base_type 	HT ou TTC
	 *	@param		int		$type				Type of line (0=product, 1=service)
	 *  @param      int		$rang            	Position of line
	 *  @param		int		$notrigger			Disable triggers
	 *	@return    	int             			>0 if OK, <0 if KO
	 *
	 *  FIXME Add field ref (that should be named ref_supplier) and label into update. For example can be filled when product line created from order.
	 */
	function addlineadd($desc, $pu, $puttc, $txtva, $txlocaltax1, $txlocaltax2, $qty, $fk_product=0, $remise_percent=0, $date_start='', $date_end='', $ventil=0, $info_bits='', $price_base_type='HT', $type=0, $rang=-1, $notrigger=false,$aData)
	{
		dol_syslog(get_class($this)."::addlineadd $desc,$pu,$puttc,$qty,$txtva,$fk_product,$remise_percent,$date_start,$date_end,$ventil,$info_bits,$price_base_type,$type", LOG_DEBUG);
		include_once DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php';

		// Clean parameters
		if (empty($remise_percent)) $remise_percent=0;
		if (empty($qty)) $qty=0;
		if (empty($info_bits)) $info_bits=0;
		if (empty($rang)) $rang=0;
		if (empty($ventil)) $ventil=0;
		if (empty($txtva)) $txtva=0;
		if (empty($txlocaltax1)) $txlocaltax1=0;
		if (empty($txlocaltax2)) $txlocaltax2=0;

		$remise_percent=price2num($remise_percent);
		$qty=price2num($qty);
		$pu=price2num($pu);
		$txtva=price2num($txtva);
		$txlocaltax1=price2num($txlocaltax1);
		$txlocaltax2=price2num($txlocaltax2);

		// Check parameters
		if ($type < 0) return -1;

		$this->db->begin();

		$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'facture_fourn_det (fk_facture_fourn)';
		$sql.= ' VALUES ('.$this->id.')';

		dol_syslog(get_class($this)."::addlineadd", LOG_DEBUG);

		$resql = $this->db->query($sql);
		if ($resql)
		{
			$idligne = $this->db->last_insert_id(MAIN_DB_PREFIX.'facture_fourn_det');

			$result=$this->updatelineadd($idligne, $desc, $pu, $puttc, $txtva, $txlocaltax1, $txlocaltax2, $qty, $fk_product, $price_base_type, $info_bits, $type, $remise_percent, true,$aData);
			if ($result > 0)
			{
				$this->rowid = $idligne;

				if (! $notrigger)
				{
					global $conf, $langs, $user;
					// Call trigger
					$result=$this->call_trigger('LINEBILL_SUPPLIER_CREATE',$user);
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
				dol_syslog("Error error=".$this->error, LOG_ERR);
				$this->db->rollback();
				return -1;
			}
		}
		else
		{
			$this->error=$this->db->lasterror();
			$this->db->rollback();
			return -2;
		}
	}

	/**
	 * Update a line detail into database
	 *
	 * @param     	int		$id            		Id of line invoice
	 * @param     	string	$desc         		Description of line
	 * @param     	double	$pu          		Prix unitaire (HT ou TTC selon price_base_type)
	 * @param     	double	$vatrate       		VAT Rate
	 * @param		double	$txlocaltax1		LocalTax1 Rate
	 * @param		double	$txlocaltax2		LocalTax2 Rate
	 * @param     	double	$qty           		Quantity
	 * @param     	int		$idproduct			Id produit
	 * @param	  	double	$price_base_type	HT or TTC
	 * @param	  	int		$info_bits			Miscellaneous informations of line
	 * @param		int		$type				Type of line (0=product, 1=service)
	 * @param     	double	$remise_percent  	Pourcentage de remise de la ligne
	 *  @param		int		$notrigger			Disable triggers
	 * @return    	int           				<0 if KO, >0 if OK
	 */
	function updatelineadd($id, $desc, $pu, $puttc, $vatrate, $txlocaltax1=0, $txlocaltax2=0, $qty=1, $idproduct=0, $price_base_type='HT', $info_bits=0, $type=0, $remise_percent=0, $notrigger=false,$aData)
	{
		global $mysoc;
		dol_syslog(get_class($this)."::updatelineadd $id,$desc,$pu,$puttc,$vatrate,$qty,$idproduct,$price_base_type,$info_bits,$type,$remise_percent", LOG_DEBUG);
		include_once DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php';

		$pu = price2num($pu);
		$puttc = price2num($puttc);
		$qty  = price2num($qty);

		$remise_percent=price2num($remise_percent);
		//echo '<br>pu qty '.$pu.' '.$qty.' |'.$idproduct.'|<br>';
		// Check parameters
		if (! is_numeric($pu) || ! is_numeric($qty)) return -16;
		if ($type < 0) return -17;

		// Clean parameters
		if (empty($vatrate)) $vatrate=0;
		if (empty($txlocaltax1)) $txlocaltax1=0;
		if (empty($txlocaltax2)) $txlocaltax2=0;
		$txlocaltax1=price2num($txlocaltax1);
		$txlocaltax2=price2num($txlocaltax2);

				// Calcul du total TTC et de la TVA pour la ligne a partir de
		// qty, pu, remise_percent et txtva
		// TRES IMPORTANT: C'est au moment de l'insertion ligne qu'on doit stocker
		// la part ht, tva et ttc, et ce au niveau de la ligne qui a son propre taux tva.

		//$localtaxes_type=getLocalTaxesFromRate($vatrate,0,$mysoc, $this->thirdparty);

		/*
		$tabprice = calcul_price_totaladd($qty, $pu, $remise_percent, $vatrate, $txlocaltax1, $txlocaltax2, 0, $price_base_type, $info_bits, $type, $this->thirdparty, $localtaxes_type);
		*/
		$total_ht  = $tabprice[0];
		$total_tva = $tabprice[1];
		$total_ttc = $tabprice[2];
		$pu_ht  = $tabprice[3];
		$pu_tva = $tabprice[4];
		$pu_ttc = $tabprice[5];
		$total_localtax1 = $tabprice[9];
		$total_localtax2 = $tabprice[10];
		$vat_src_code = $aData->vat_src_code;
		$aData->localtax1_tx+=0;
		$aData->localtax2_tx+=0;
		if (empty($aData->tva_tx)) $aData->tva_tx = 0;
		if (empty($info_bits)) $info_bits=0;

		if ($idproduct)
		{
			$product=new Product($this->db);
			$result=$product->fetch($idproduct);
			$product_type = $product->type;
		}
		else
		{
			$product_type = $type;
		}

		$this->db->begin();
		$total_ht = $aData->total_ttc - $aData->total_localtax5- $aData->total_localtax4- $aData->total_localtax3- $aData->total_localtax2- $aData->total_localtax1-$aData->total_tva;
		$sql = "UPDATE ".MAIN_DB_PREFIX."facture_fourn_det SET";
		$sql.= " description ='".$this->db->escape($desc)."'";
		$sql.= ", fk_unit = ".$aData->fk_unit;
		$sql.= ", pu_ht = ".price2num($aData->subprice);
		$sql.= ", pu_ttc = ".price2num($aData->price);
		$sql.= ", qty = ".price2num($qty);
		$sql.= ", remise_percent = ".price2num($remise_percent);
		$sql.= ", vat_src_code = ".(! empty($vat_src_code)?"'".$vat_src_code."'":"null");
		$sql .= ", tva_tx = ".price2num($aData->tva_tx);
		$sql.= ", localtax1_tx = ".price2num($aData->localtax1_tx);
		$sql.= ", localtax2_tx = ".price2num($aData->localtax2_tx);
		$sql.= ", localtax1_type = ' ".$aData->localtax1_type."'";
		$sql.= ", localtax2_type = ' ".$aData->localtax2_type."'";
		$sql.= ", total_ht = ".price2num($total_ht);
		$sql.= ", tva= ".price2num($aData->total_tva+0);
		$sql.= ", total_localtax1= ".price2num($aData->total_localtax1+0);
		$sql.= ", total_localtax2= ".price2num($aData->total_localtax2+0);
		$sql.= ", total_ttc = ".price2num($aData->total_ttc+0);
		if ($idproduct) $sql.= ", fk_product = ".$idproduct;
		else $sql.= ", fk_product = null";
		$sql.= ", product_type = ".$product_type;
		$sql.= ", info_bits = ".$info_bits;
		$sql.= " WHERE rowid = ".$id;
		//echo $sql;exit;
		dol_syslog(get_class($this)."::updatelineadd", LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$this->rowid = $id;

			if (! $notrigger)
			{
				global $conf, $langs, $user;
				// Call trigger
				$result=$this->call_trigger('LINEBILL_SUPPLIER_UPDATE',$user);
				if ($result < 0)
				{
					$this->db->rollback();
					return -1;
				}
				// End call triggers
			}
			//actualizamos la cabecera

			// Update total price into invoice record
			//$result=$this->update_priceadd('','auto');
			$result = 1;
			$this->db->commit();

			return $result;
		}
		else
		{
			$this->db->rollback();
			$this->error=$this->db->lasterror();
			return -2;
		}
	}


	//adicional al objectcom
	/* This is to show array of line of details */


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
		global $conf, $hookmanager, $langs, $user,$objectdetadd, $objectadd;
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

		print '<td class="linecoledit"></td>';  // No width to allow autodim

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
		$lAdd = false;

		foreach ($this->lines as $line)
		{
			//Line extrafield
			$line->fetch_optionals($line->id,$extralabelslines);
			$resadd = $objectdetadd->fetch('',$line->id);
			if ($resadd>0)
			{
				$line->amount_ice = $objectdetadd->amount_ice;
				$line->discount = $objectdetadd->discount;
				//revisar si corresponde RQC
				//$line->total_ttc = $line->total_ttc - $objectdetadd->discount;

				if ($objectdetadd->object == 'requestcashdeplacement' && $objectdetadd->fk_object>0)
				{
					if ($objectadd->code_facture != $conf->global->FISCAL_CODE_FACTURE_ENERGY)
					{
						$lEdit = false;

						$disableedit = true;
						$disableremove = false;
						$this->situation_counter = 0;
						$this->situation_cycle_ref = 1;
						$lAdd = $objectdetasdd->fk_object;
					}
				}
			}
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
		return $lAdd;
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
			$dirtpls=array_merge($conf->modules_parts['tpl'],array('/purchase/tpl'));
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
			//$line->pu_ttc = price2num($line->subprice * (1 + ($line->tva_tx/100)), 'MU');

			// Output template part (modules that overwrite templates must declare this into descriptor)
			// Use global variables + $dateSelector + $seller and $buyer
			$dirtpls=array_merge($conf->modules_parts['tpl'],array('/purchase/tpl'));
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
     *    Load object in memory from database
     *
     *    @param	int		$id         Id supplier invoice
     *    @param	string	$ref		Ref supplier invoice
     *    @return   int        			<0 if KO, >0 if OK, 0 if not found
     */
    function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND', $filterstatic='', $lView= false)
    {
        global $langs;

        $sql = "SELECT";
        $sql.= " t.rowid,";
		$sql.= " t.ref,";
        $sql.= " t.ref_supplier,";
        $sql.= " t.entity,";
        $sql.= " t.type,";
        $sql.= " t.fk_soc,";
        $sql.= " t.datec,";
        $sql.= " t.datef,";
        $sql.= " t.tms,";
        $sql.= " t.libelle,";
        $sql.= " t.paye,";
        $sql.= " t.amount,";
        $sql.= " t.remise,";
        $sql.= " t.close_code,";
        $sql.= " t.close_note,";
        $sql.= " t.tva,";
        $sql.= " t.localtax1,";
        $sql.= " t.localtax2,";
        $sql.= " t.total,";
        $sql.= " t.total_ht,";
        $sql.= " t.total_tva,";
        $sql.= " t.total_ttc,";
        $sql.= " t.fk_statut,";
        $sql.= " t.fk_user_author,";
        $sql.= " t.fk_user_valid,";
        $sql.= " t.fk_facture_source,";
        $sql.= " t.fk_projet,";
        $sql.= " t.fk_cond_reglement,";
        $sql.= " t.fk_account,";
        $sql.= " t.fk_mode_reglement,";
        $sql.= " t.date_lim_reglement,";
        $sql.= " t.note_private,";
        $sql.= " t.note_public,";
        $sql.= " t.model_pdf,";
        $sql.= " t.import_key,";
        $sql.= " t.extraparams,";
        $sql.= " cr.code as cond_reglement_code, cr.libelle as cond_reglement_libelle,";
        $sql.= " p.code as mode_reglement_code, p.libelle as mode_reglement_libelle,";
        $sql.= ' s.nom as socnom, s.rowid as socid,';
        $sql.= ' t.fk_incoterms, t.location_incoterms,';
        $sql.= " i.libelle as libelle_incoterms,";
        $sql.= ' t.fk_multicurrency, t.multicurrency_code, t.multicurrency_tx, t.multicurrency_total_ht, t.multicurrency_total_tva, t.multicurrency_total_ttc';
        $sql.= ' FROM '.MAIN_DB_PREFIX.'facture_fourn as t';
        $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON (t.fk_soc = s.rowid)";
        $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_payment_term as cr ON (t.fk_cond_reglement = cr.rowid)";
        $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_paiement as p ON (t.fk_mode_reglement = p.id)";
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_incoterms as i ON t.fk_incoterms = i.rowid';


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
        $resql=$this->db->query($sql);
        //echo '<hr>'.$sql;
        if ($resql)
        {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql))
			{
				$line = new FactureFournisseurext($this->db);


                $line->id					= $obj->rowid;
                $line->ref					= $obj->ref?$obj->ref:$obj->rowid;	// We take rowid if ref is empty for backward compatibility

                $line->ref_supplier			= $obj->ref_supplier;
                $line->entity				= $obj->entity;
                $line->type					= empty($obj->type)? self::TYPE_STANDARD:$obj->type;
                $line->fk_soc				= $obj->fk_soc;
                $line->datec				= $this->db->jdate($obj->datec);
                $line->date					= $this->db->jdate($obj->datef);
                $line->datep				= $this->db->jdate($obj->datef);
                $line->tms					= $this->db->jdate($obj->tms);
                $line->libelle				= $obj->libelle;
                $line->label				= $obj->libelle;
                $line->paye					= $obj->paye;
                $line->amount				= $obj->amount;
                $line->remise				= $obj->remise;
                $line->close_code			= $obj->close_code;
                $line->close_note			= $obj->close_note;
                $line->tva					= $obj->tva;
                $line->total_localtax1		= $obj->localtax1;
                $line->total_localtax2		= $obj->localtax2;
                $line->total				= $obj->total;
                $line->total_ht				= $obj->total_ht;
                $line->total_tva			= $obj->total_tva;
                $line->total_ttc			= $obj->total_ttc;
                $line->fk_statut			= $obj->fk_statut;
                $line->statut				= $obj->fk_statut;
                $line->fk_user_author		= $obj->fk_user_author;
                $line->author				= $obj->fk_user_author;
                $line->fk_user_valid		= $obj->fk_user_valid;
                $line->fk_facture_source	= $obj->fk_facture_source;
                $line->fk_project			= $obj->fk_projet;
	            $line->cond_reglement_id	= $obj->fk_cond_reglement;
	            $line->cond_reglement_code	= $obj->cond_reglement_code;
	            $line->cond_reglement		= $obj->cond_reglement_libelle;
	            $line->cond_reglement_doc	= $obj->cond_reglement_libelle;
                $line->fk_account           = $obj->fk_account;
	            $line->mode_reglement_id	= $obj->fk_mode_reglement;
	            $line->mode_reglement_code	= $obj->mode_reglement_code;
	            $line->mode_reglement		= $obj->mode_reglement_libelle;
                $line->date_echeance		= $this->db->jdate($obj->date_lim_reglement);
                $line->note					= $obj->note_private;	// deprecated
                $line->note_private			= $obj->note_private;
                $line->note_public			= $obj->note_public;
                $line->model_pdf			= $obj->model_pdf;
                $line->modelpdf			    = $obj->model_pdf;
                $line->import_key			= $obj->import_key;

				//Incoterms
				$line->fk_incoterms = $obj->fk_incoterms;
				$line->location_incoterms = $obj->location_incoterms;
				$line->libelle_incoterms = $obj->libelle_incoterms;

				// Multicurrency
				$line->fk_multicurrency 		= $obj->fk_multicurrency;
				$line->multicurrency_code 		= $obj->multicurrency_code;
				$line->multicurrency_tx 		= $obj->multicurrency_tx;
				$line->multicurrency_total_ht 	= $obj->multicurrency_total_ht;
				$line->multicurrency_total_tva 	= $obj->multicurrency_total_tva;
				$line->multicurrency_total_ttc 	= $obj->multicurrency_total_ttc;

                $line->extraparams			= (array) json_decode($obj->extraparams, true);

                $line->socid  = $obj->socid;
                $line->socnom = $obj->socnom;


                // Retreive all extrafield
                // fetch optionals attributes and labels
                require_once(DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php');
                $extrafields=new ExtraFields($this->db);
                $extralabels=$extrafields->fetch_name_optionals_label($this->table_element,true);
                $this->fetch_optionals($this->id,$extralabels);

                if ($line->statut == self::STATUS_DRAFT) $line->brouillon = 1;

                $result=$line->fetch_lines_();
                if ($result < 0)
                {
                    $this->error=$this->db->lasterror();
                    return -3;
                }
                if ($lView)
                {
                	if ($num == 1) $this->fetch($obj->rowid);
                }

                $this->lines[$line->id] = $line;
            }

            $this->db->free($resql);
            return $num;
        }
        else
        {
            $this->error="Error ".$this->db->lasterror();
            return -1;
        }
    }

   	//devuelve una lista de facturas segun el tipo de code_type_purchase
	//y segun un campo seleccionado y la condicion
	function getlist_facturefourn_typepurchase($code,$status=1,$filter='')
	{
		global $conf;
		$sql = "SELECT";
		$sql.= " t.rowid ";
		$sql.= " FROM ".MAIN_DB_PREFIX."facture_fourn AS t ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."facture_fourn_add AS ff ON ff.fk_facture_fourn = t.rowid ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."facture_fourn_det AS td ON td.fk_facture_fourn = t.rowid ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."facture_fourn_det_add AS ffd ON ffd.fk_facture_fourn_det = td.rowid ";
		$sql.= " WHERE ff.code_type_purchase = '".$code."'";
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND t.entity IN (" . getEntity("facturefourn", 1) . ")";
		}
		if ($status>0) $sql.= " AND t.fk_statut >= ".$status;
		if ($filter) $sql.= $filter;
		$this->aArray = array();
		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$i = 0;
				while($i < $numrows)
				{
					$obj = $this->db->fetch_object($resql);
					$this->aArray[$obj->rowid] = $obj->rowid;
					$i++;
				}
			}
			return $numrows;
		}
		return -1;
	}

    /**
     *	Load this->lines
     * Recupera las lineas de la factura fournisseur con la tabla adicional
     *	@return     int         1 si ok, < 0 si erreur
     */
    function fetch_lines_()
    {
        $sql = "SELECT f.rowid, f.ref as ref_supplier, f.description, f.pu_ht, f.pu_ttc, f.qty, f.remise_percent, f.vat_src_code, f.tva_tx";
        $sql.= ", f.localtax1_tx, f.localtax2_tx, f.total_localtax1, f.total_localtax2, f.fk_facture_fourn ";
        $sql.= ", f.total_ht, f.tva as total_tva, f.total_ttc, f.fk_product, f.product_type, f.info_bits ";
        $sql.= ", f.rang, f.special_code, f.fk_parent_line, f.fk_unit";
        $sql.= ", p.rowid as product_id, p.ref as product_ref, p.label as label, p.description as product_desc";
		$sql.= ", f.fk_multicurrency, f.multicurrency_code, f.multicurrency_subprice, f.multicurrency_total_ht";
		$sql.= ", f.multicurrency_total_tva, f.multicurrency_total_ttc ";
		$sql.= ", fa.fk_object, fa.object, fa.fk_fabrication, fa.fk_fabricationdet, fa.fk_projet, fa.fk_projet_task ";
		$sql.= ", fa.fk_jobs, fa.fk_jobsdet, fa.fk_structure, fa.fk_poa, fa.partida, fa.amount_ice, fa.discount ";

        $sql.= " FROM ".MAIN_DB_PREFIX."facture_fourn_det as f";
        $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."facture_fourn_det_add as fa ON fa.fk_facture_fourn_det = f.rowid";
        $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."product as p ON f.fk_product = p.rowid";
        $sql.= " WHERE fk_facture_fourn=".$this->id;
        $sql.= " ORDER BY f.rang, f.rowid";


        dol_syslog(get_class($this)."::fetch_lines", LOG_DEBUG);
        $resql_rows = $this->db->query($sql);
        if ($resql_rows)
        {
            $num_rows = $this->db->num_rows($resql_rows);
            if ($num_rows)
            {
                $i = 0;
                while ($i < $num_rows)
                {
                    $obj = $this->db->fetch_object($resql_rows);

	                $line = new SupplierInvoiceLine($this->db);

                    $line->id				= $obj->rowid;
                    $line->rowid			= $obj->rowid;
                    $line->description		= $obj->description;
                    $line->product_ref		= $obj->product_ref;
                    $line->ref				= $obj->product_ref;
                    $line->ref_supplier		= $obj->ref_supplier;
                    $line->libelle			= $obj->label;
                    $line->label  			= $obj->label;
                    $line->product_desc		= $obj->product_desc;
                    $line->subprice			= $obj->pu_ht;
                    $line->pu_ht			= $obj->pu_ht;
                    $line->pu_ttc			= $obj->pu_ttc;

                    $line->vat_src_code     = $obj->vat_src_code;
                    $line->tva_tx			= $obj->tva_tx;
                    $line->localtax1_tx		= $obj->localtax1_tx;
                    $line->localtax2_tx		= $obj->localtax2_tx;
                    $line->qty				= $obj->qty;
                    $line->remise_percent   = $obj->remise_percent;
                    $line->tva				= $obj->total_tva;
                    $line->total_ht			= $obj->total_ht;
                    $line->total_tva		= $obj->total_tva;
                    $line->total_localtax1	= $obj->total_localtax1;
                    $line->total_localtax2	= $obj->total_localtax2;
                    $line->fk_facture_fourn     = $obj->fk_facture_fourn;
                    $line->total_ttc		= $obj->total_ttc;
                    $line->fk_product		= $obj->fk_product;
                    $line->product_type		= $obj->product_type;
                    $line->product_label	= $obj->label;
                    $line->info_bits		= $obj->info_bits;
                    $line->fk_parent_line   = $obj->fk_parent_line;
                    $line->special_code		= $obj->special_code;
                    $line->rang       		= $obj->rang;
                    $line->fk_unit          = $obj->fk_unit;

					// Multicurrency
					$line->fk_multicurrency 		= $obj->fk_multicurrency;
					$line->multicurrency_code 		= $obj->multicurrency_code;
					$line->multicurrency_subprice 	= $obj->multicurrency_subprice;
					$line->multicurrency_total_ht 	= $obj->multicurrency_total_ht;
					$line->multicurrency_total_tva 	= $obj->multicurrency_total_tva;
					$line->multicurrency_total_ttc 	= $obj->multicurrency_total_ttc;
					//tabla adicional
					$line->fk_object = $obj->fk_object;
					$line->object = $obj->object;
					$line->fk_fabrication = $obj->fk_fabrication;
					$line->fk_fabricationdet = $obj->fk_fabricationdet;
					$line->fk_projet = $obj->fk_projet;
					$line->fk_projet_task = $obj->fk_projet_task;
					$line->fk_jobs = $obj->fk_jobs;
					$line->fk_jobsdet = $obj->fk_jobsdet;
					$line->fk_structure = $obj->fk_structure;
					$line->fk_poa = $obj->fk_poa;
					$line->partida = $obj->partida;
					$line->amount_ice = $obj->amount_ice;
					$line->discount = $obj->discount;

	                $this->lines[$i] = $line;

                    $i++;
                }
            }
            $this->db->free($resql_rows);
            return 1;
        }
        else
        {
            $this->error=$this->db->error();
            return -3;
        }
    }


}
?>