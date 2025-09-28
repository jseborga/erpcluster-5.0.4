<?php
require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';

class Contratext extends Contrat
{

	public $linec;
	public $linesdet;

	/**
	 *	Return clicable name (with picto eventually)
	 *
	 *	@param	int		$withpicto		0=No picto, 1=Include picto into link, 2=Only picto
	 *	@param	int		$maxlength		Max length of ref
	 *	@return	string					Chaine avec URL
	 */
	function getNomUrladd($withpicto=0,$maxlength=0)
	{
		global $langs;

		$result='';
        $label=$langs->trans("ShowContract").': '.$this->ref;

        $link = '<a href="'.DOL_URL_ROOT.'/sales/contrat/card.php?id='.$this->id.'" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip">';
		$linkend='</a>';

		$picto='contract';


		if ($withpicto) $result.=($link.img_object($label, $picto, 'class="classfortooltip"').$linkend);
		if ($withpicto && $withpicto != 2) $result.=' ';
		if ($withpicto != 2) $result.=$link.($maxlength?dol_trunc($this->ref,$maxlength):$this->ref).$linkend;
		return $result;
	}
	    /* This is to show array of line of details of source object */


    /**
     * 	Return HTML table table of source object lines
     *  TODO Move this and previous function into output html class file (htmlline.class.php).
     *  If lines are into a template, title must also be into a template
     *  But for the moment we don't know if it's possible as we keep a method available on overloaded objects.
     *
     *  @return	void
     */
    function printOriginLinesListadd()
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
        print '<td align="right">'.$langs->trans('ReductionShort').'</td></tr>';

        $var = true;
        $i	 = 0;

        foreach ($this->lines as $line)
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

        if (($line->info_bits & 2) == 2)  // TODO Not sure this is used for source object
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

        // Output template part (modules that overwrite templates must declare this into descriptor)
        // Use global variables + $dateSelector + $seller and $buyer
        $dirtpls=array_merge($conf->modules_parts['tpl'],array('/core/tpl'));
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
	 *  Ajoute une ligne de contrat en base
	 *
	 *  @param	string		$desc            	Description de la ligne
	 *  @param  float		$pu_ht              Prix unitaire HT
	 *  @param  int			$qty             	Quantite
	 *  @param  float		$txtva           	Taux tva
	 *  @param  float		$txlocaltax1        Local tax 1 rate
	 *  @param  float		$txlocaltax2        Local tax 2 rate
	 *  @param  int			$fk_product      	Id produit
	 *  @param  float		$remise_percent  	Pourcentage de remise de la ligne
	 *  @param  int			$date_start      	Date de debut prevue
	 *  @param  int			$date_end        	Date de fin prevue
	 *	@param	string		$price_base_type	HT or TTC
	 * 	@param  float		$pu_ttc             Prix unitaire TTC
	 * 	@param  int			$info_bits			Bits de type de lignes
	 * 	@param  int			$fk_fournprice		Fourn price id
	 *  @param  int			$pa_ht				Buying price HT
	 *  @param	array		$array_options		extrafields array
	 * 	@param 	string		$fk_unit 			Code of the unit to use. Null to use the default one
	 *  @return int             				<0 si erreur, >0 si ok
	 */
	
	function addlineadd($desc, $pu_ht, $qty, $txtva, $txlocaltax1, $txlocaltax2, $fk_product, $remise_percent, $date_start, $date_end, $price_base_type='HT', $pu_ttc=0.0, $info_bits=0, $fk_fournprice=null, $pa_ht = 0,$array_options=0, $fk_unit = null,$lines)
	{
		global $user, $langs, $conf, $mysoc;

		dol_syslog(get_class($this)."::addline $desc, $pu_ht, $qty, $txtva, $txlocaltax1, $txlocaltax2, $fk_product, $remise_percent, $date_start, $date_end, $price_base_type, $pu_ttc, $info_bits");

		if ($this->statut >= 0)
		{
			$this->db->begin();

			// Clean parameters
			$pu_ht=price2num($pu_ht);
			$pu_ttc=price2num($pu_ttc);
			$pa_ht=price2num($pa_ht);
			$txtva=price2num($txtva);
			$txlocaltax1=price2num($txlocaltax1);
			$txlocaltax2=price2num($txlocaltax2);
			$remise_percent=price2num($remise_percent);
			$qty=price2num($qty);
			if (empty($qty)) $qty=1;
			if (empty($info_bits)) $info_bits=0;
			if (empty($pu_ht) || ! is_numeric($pu_ht))  $pu_ht=0;
			if (empty($pu_ttc)) $pu_ttc=0;
            if (empty($txlocaltax1) || ! is_numeric($txlocaltax1)) $txlocaltax1=0;
            if (empty($txlocaltax2) || ! is_numeric($txlocaltax2)) $txlocaltax2=0;

			if ($price_base_type=='HT')
			{
				$pu=$pu_ht;
			}
			else
			{
				$pu=$pu_ttc;
			}

			// Check parameters
			if (empty($remise_percent)) $remise_percent=0;

			// Calcul du total TTC et de la TVA pour la ligne a partir de
			// qty, pu, remise_percent et txtva
			// TRES IMPORTANT: C'est au moment de l'insertion ligne qu'on doit stocker
			// la part ht, tva et ttc, et ce au niveau de la ligne qui a son propre taux tva.

			//$localtaxes_type=getLocalTaxesFromRate($txtva, 0, $this->societe, $mysoc);
			//$txtva = preg_replace('/\s*\(.*\)/','',$txtva);  // Remove code into vatrate.
					
			//$tabprice=calcul_price_total($qty, $pu, $remise_percent, $txtva, $txlocaltax1, $txlocaltax2, 0, $price_base_type, $info_bits, 1,$mysoc, $localtaxes_type);
			$total_ht  = $tabprice[0];
			$total_tva = $tabprice[1];
			$total_ttc = $tabprice[2];
			$total_localtax1= $tabprice[9];
			$total_localtax2= $tabprice[10];

			$localtax1_type=$localtaxes_type[0];
			$localtax2_type=$localtaxes_type[2];

			$total_ht  = $lines->total_ht;
			$total_tva = $lines->total_tva;
			$total_ttc = $lines->total_ttc;
			$total_localtax1= $lines->total_localtax1+0;
			$total_localtax2= $lines->total_localtax2+0;

			$localtax1_type=$lines->localtax1_type;
			$localtax2_type=$lines->localtax2_type;

			// TODO A virer
			// Anciens indicateurs: $price, $remise (a ne plus utiliser)
			$remise = $lines->remise;
			$remise_percent = $lines->remise_percent;
			$price = price2num(round($pu_ht, 2));
			if (dol_strlen($remise_percent) > 0)
			{
				$remise = round(($pu_ht * $remise_percent / 100), 2);
				$price = $pu_ht - $remise;
			}

		    if (empty($pa_ht)) $pa_ht=0;

			
			// if buy price not defined, define buyprice as configured in margin admin
			if ($this->pa_ht == 0) 
			{
				if (($result = $this->defineBuyPrice($pu_ht, $remise_percent, $fk_product)) < 0)
				{
					return $result;
				}
				else
				{
					$pa_ht = $result;
				}
			}

			// Insertion dans la base
			$sql = "INSERT INTO ".MAIN_DB_PREFIX."contratdet";
			$sql.= " (fk_contrat, label, description, fk_product, qty, tva_tx,";
			$sql.= " localtax1_tx, localtax2_tx, localtax1_type, localtax2_type, remise_percent, subprice,";
			$sql.= " total_ht, total_tva, total_localtax1, total_localtax2, total_ttc,";
			$sql.= " info_bits,";
			$sql.= " price_ht, remise, fk_product_fournisseur_price, buy_price_ht";
			if ($date_start > 0) { $sql.= ",date_ouverture_prevue"; }
			if ($date_end > 0)   { $sql.= ",date_fin_validite"; }
			$sql.= ", fk_unit";
			$sql.= ") VALUES (";
			$sql.= $this->id.", '', '" . $this->db->escape($desc) . "',";
			$sql.= ($fk_product>0 ? $fk_product : "null").",";
			$sql.= " ".$qty.",";
			$sql.= " ".$txtva.",";
			$sql.= " ".$txlocaltax1.",";
			$sql.= " ".$txlocaltax2.",";
			$sql.= " '".$localtax1_type."',";
			$sql.= " '".$localtax2_type."',";
			$sql.= " ".price2num($remise_percent).",";
			$sql.= " ".price2num($pu_ht).",";
			$sql.= " ".price2num($total_ht).",".price2num($total_tva).",".price2num($total_localtax1).",".price2num($total_localtax2).",".price2num($total_ttc).",";
			$sql.= " '".$info_bits."',";
			$sql.= " ".price2num($price).",".price2num($remise).",";
			if (isset($fk_fournprice)) $sql.= ' '.$fk_fournprice.',';
			else $sql.= ' null,';
			if (isset($pa_ht)) $sql.= ' '.price2num($pa_ht);
			else $sql.= ' null';
			if ($date_start > 0) { $sql.= ",'".$this->db->idate($date_start)."'"; }
			if ($date_end > 0) { $sql.= ",'".$this->db->idate($date_end)."'"; }
			$sql.= ", ".($fk_unit?"'".$this->db->escape($fk_unit)."'":"null");
			$sql.= ")";

			$resql=$this->db->query($sql);
			if ($resql)
			{
				$result=$this->update_statut($user);
				if ($result > 0)
				{

					if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED) && is_array($array_options) && count($array_options)>0) // For avoid conflicts if trigger used
					{
						$contractline = new ContratLigne($this->db);
						$contractline->array_options=$array_options;
						$contractline->id= $this->db->last_insert_id(MAIN_DB_PREFIX.$contractline->table_element);
						$result=$contractline->insertExtraFields();
						if ($result < 0)
						{
							$this->error[]=$contractline->error;
							$error++;
						}
					}

					if (empty($error)) {
					    // Call trigger
					    $result=$this->call_trigger('LINECONTRACT_INSERT',$user);
					    if ($result < 0)
					    {
					        $this->db->rollback();
					        return -1;
					    }
					    // End call triggers

						$this->db->commit();
						return 1;
					}
				}
				else
				{
					$this->db->rollback();
					return -1;
				}
			}
			else
			{
				$this->db->rollback();
				$this->error=$this->db->error()." sql=".$sql;
				return -1;
			}
		}
		else
		{
			dol_syslog(get_class($this)."::addlineadd ErrorTryToAddLineOnValidatedContract", LOG_ERR);
			return -2;
		}
	}

	//para proyectos

	//Funcion para buscar por fk_projet
	function getlist($id)
	{
		$sql = "SELECT rowid, statut, ref, fk_soc, mise_en_service as datemise,";
		$sql.= " fk_user_mise_en_service, date_contrat as datecontrat,";
		$sql.= " fk_user_author,";
		$sql.= " fk_projet,";
		$sql.= " fk_commercial_signature, fk_commercial_suivi,";
		$sql.= " note_private, note_public, model_pdf, extraparams";
		$sql.= " ,ref_supplier";
		$sql.= " ,ref_ext";
		$sql.= " FROM ".MAIN_DB_PREFIX."contrat";
		$sql.= " WHERE fk_projet =".$id;
		
		dol_syslog(get_class($this)."::getlist", LOG_DEBUG);
		$resql = $this->db->query($sql);
		$this->linec = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			while ($obj = $this->db->fetch_object($resql))
			{
				$lin = new Contrat($this->db);
				$lin->id						= $obj->rowid;
				$lin->ref						= (!isset($obj->ref) || !$obj->ref) ? $obj->rowid : $obj->ref;
				$lin->ref_supplier				= $obj->ref_supplier;
				$lin->ref_ext					= $obj->ref_ext;
				$lin->statut					= $obj->statut;
				$lin->mise_en_service			= $this->db->jdate($obj->datemise);
				
				$lin->date_contrat				= $this->db->jdate($obj->datecontrat);
				$lin->date_creation				= $this->db->jdate($obj->datecontrat);
				
				$lin->user_author_id			= $obj->fk_user_author;
				
				$lin->commercial_signature_id	= $obj->fk_commercial_signature;
				$lin->commercial_suivi_id		= $obj->fk_commercial_suivi;
				
				$lin->note_private				= $obj->note_private;
				$lin->note_public				= $obj->note_public;
				$lin->modelpdf					= $obj->model_pdf;
				
				$lin->fk_projet				= $obj->fk_projet; 
				// deprecated
				$lin->fk_project				= $obj->fk_projet;

				$lin->socid					= $obj->fk_soc;
				$lin->fk_soc					= $obj->fk_soc;

				$lin->extraparams				= (array) json_decode($obj->extraparams, true);
				//$this->db->free($resql);
				// Retreive all extrafield for thirdparty
				// fetch optionals attributes and labels
				require_once(DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php');
				$extrafields=new ExtraFields($this->db);
				$extralabels=$extrafields->fetch_name_optionals_label($this->table_element,true);
				$numrows = $lin->fetch_optionals($lin->id,$extralabels);

				// Lines
				$this->lines  = array();
				$lin->lines = array();
				$lines = $this->fetch_linec($obj->rowid);
				$lin->lines = $lines;
				$this->linec[$obj->rowid] = $lin;
			}
			return 1;
		}
		else
		{
			dol_syslog(get_class($this)."::Fetch Erreur lecture contrat");
			$this->error=$this->db->error();
			return -1;
		}

	}

	/**
	 *  Load lines array into this->lines
	 *
	 *  @return ContratLigne[]   Return array of contract lines
	 */
	function fetch_linec($id)
	{
		$this->nbofserviceswait=0;
		$this->nbofservicesopened=0;
		$this->nbofservicesexpired=0;
		$this->nbofservicesclosed=0;

		$total_ttc=0;
		$total_vat=0;
		$total_ht=0;

		$now=dol_now();

		require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
		$extrafieldsline=new ExtraFields($this->db);
		$line = new ContratLigne($this->db);
		$extralabelsline=$extrafieldsline->fetch_name_optionals_label($line->table_element,true);

		$this->lines=array();

		// Selectionne les lignes contrats liees a un produit
		$sql = "SELECT p.label as product_label, p.description as product_desc, p.ref as product_ref,";
		$sql.= " d.rowid, d.fk_contrat, d.statut, d.description, d.price_ht, d.tva_tx, d.localtax1_tx, d.localtax2_tx, d.qty, d.remise_percent, d.subprice, d.fk_product_fournisseur_price as fk_fournprice, d.buy_price_ht as pa_ht,";
		$sql.= " d.total_ht,";
		$sql.= " d.total_tva,";
		$sql.= " d.total_localtax1,";
		$sql.= " d.total_localtax2,";
		$sql.= " d.total_ttc,";
		$sql.= " d.info_bits, d.fk_product,";
		$sql.= " d.date_ouverture_prevue, d.date_ouverture,";
		$sql.= " d.date_fin_validite, d.date_cloture,";
		$sql.= " d.fk_user_author,";
		$sql.= " d.fk_user_ouverture,";
		$sql.= " d.fk_user_cloture,";
		$sql.= " d.fk_unit";
		$sql.= " FROM ".MAIN_DB_PREFIX."contratdet as d ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product as p ON d.fk_product = p.rowid ";
		$sql.= " WHERE d.fk_contrat = ".$id;
		$sql.= " AND d.fk_product = p.rowid";
		$sql.= " ORDER by d.rowid ASC";
		dol_syslog(get_class($this)."::fetch_linec", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;

			while ($i < $num)
			{
				$objp					= $this->db->fetch_object($result);

				$line					= new ContratLigne($this->db);
				$line->id				= $objp->rowid;
				$line->ref				= $objp->rowid;
				$line->fk_contrat		= $objp->fk_contrat;
				$line->desc				= $objp->description;  // Description ligne
				$line->qty				= $objp->qty;
				$line->tva_tx			= $objp->tva_tx;
				$line->localtax1_tx		= $objp->localtax1_tx;
				$line->localtax2_tx		= $objp->localtax2_tx;
				$line->subprice			= $objp->subprice;
				$line->statut			= $objp->statut;
				$line->remise_percent	= $objp->remise_percent;
				$line->price_ht			= $objp->price_ht;
				$line->price			= $objp->price_ht;	// For backward compatibility
				$line->total_ht			= $objp->total_ht;
				$line->total_tva		= $objp->total_tva;
				$line->total_localtax1	= $objp->total_localtax1;
				$line->total_localtax2	= $objp->total_localtax2;
				$line->total_ttc		= $objp->total_ttc;
				$line->fk_product		= $objp->fk_product;
				$line->info_bits		= $objp->info_bits;

				$line->fk_fournprice 	= $objp->fk_fournprice;
				$marginInfos = getMarginInfos($objp->subprice, $objp->remise_percent, $objp->tva_tx, $objp->localtax1_tx, $objp->localtax2_tx, $line->fk_fournprice, $objp->pa_ht);
				$line->pa_ht 			= $marginInfos[0];

				$line->fk_user_author	= $objp->fk_user_author;
				$line->fk_user_ouverture= $objp->fk_user_ouverture;
				$line->fk_user_cloture  = $objp->fk_user_cloture;
				$line->fk_unit           = $objp->fk_unit;

				$line->ref				= $objp->product_ref;			// deprecated
				$line->label			= $objp->product_label;         // deprecated
				$line->libelle			= $objp->product_label;         // deprecated
				$line->product_ref		= $objp->product_ref;   // Ref product
				$line->product_desc		= $objp->product_desc;  // Description product
				$line->product_label	= $objp->product_label; // Label product

				$line->description		= $objp->description;

				$line->date_ouverture_prevue = $this->db->jdate($objp->date_ouverture_prevue);
				$line->date_ouverture        = $this->db->jdate($objp->date_ouverture);
				$line->date_fin_validite     = $this->db->jdate($objp->date_fin_validite);
				$line->date_cloture          = $this->db->jdate($objp->date_cloture);
				// For backward compatibility
				$line->date_debut_prevue = $this->db->jdate($objp->date_ouverture_prevue);
				$line->date_debut_reel   = $this->db->jdate($objp->date_ouverture);
				$line->date_fin_prevue   = $this->db->jdate($objp->date_fin_validite);
				$line->date_fin_reel     = $this->db->jdate($objp->date_cloture);

				// Retreive all extrafield for propal
				// fetch optionals attributes and labels
				$line->fetch_optionals($line->id,$extralabelsline);

				$this->lines[$objp->rowid]			= $line;

				//dol_syslog("1 ".$line->desc);
				//dol_syslog("2 ".$line->product_desc);

				if ($line->statut == 0) $this->nbofserviceswait++;
				if ($line->statut == 4 && (empty($line->date_fin_prevue) || $line->date_fin_prevue >= $now)) $this->nbofservicesopened++;
				if ($line->statut == 4 && (! empty($line->date_fin_prevue) && $line->date_fin_prevue < $now)) $this->nbofservicesexpired++;
				if ($line->statut == 5) $this->nbofservicesclosed++;

				$total_ttc+=$objp->total_ttc;   // TODO Not saved into database
				$total_vat+=$objp->total_tva;
				$total_ht+=$objp->total_ht;

				$i++;
			}
			$this->db->free($result);
		}
		else
		{
			dol_syslog(get_class($this)."::Fetch Erreur lecture des lignes de contrats liees aux produits");
			return -3;
		}

		// Selectionne les lignes contrat liees a aucun produit
		$sql = "SELECT d.rowid, d.fk_contrat, d.statut, d.qty, d.description, d.price_ht, d.tva_tx, d.localtax1_tx, d.localtax2_tx, d.rowid, d.remise_percent, d.subprice,";
		$sql.= " d.total_ht,";
		$sql.= " d.total_tva,";
		$sql.= " d.total_localtax1,";
		$sql.= " d.total_localtax2,";
		$sql.= " d.total_ttc,";
		$sql.= " d.info_bits, d.fk_product,";
		$sql.= " d.date_ouverture_prevue, d.date_ouverture,";
		$sql.= " d.date_fin_validite, d.date_cloture,";
		$sql.= " d.fk_user_author,";
		$sql.= " d.fk_user_ouverture,";
		$sql.= " d.fk_user_cloture,";
		$sql.= " d.fk_unit";
		$sql.= " FROM ".MAIN_DB_PREFIX."contratdet as d";
		$sql.= " WHERE d.fk_contrat = ".$id;
		$sql.= " AND (d.fk_product IS NULL OR d.fk_product = 0)";   // fk_product = 0 gardee pour compatibilitee
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;

			while ($i < $num)
			{
				$objp                  = $this->db->fetch_object($result);

				$line                 = new ContratLigne($this->db);
				$line->id 			  = $objp->rowid;
				$line->fk_contrat     = $objp->fk_contrat;
				$line->libelle        = $objp->description;
				$line->desc           = $objp->description;
				$line->qty            = $objp->qty;
				$line->statut 		  = $objp->statut;
				$line->ref            = '';
				$line->tva_tx         = $objp->tva_tx;
				$line->localtax1_tx   = $objp->localtax1_tx;
				$line->localtax2_tx   = $objp->localtax2_tx;
				$line->subprice       = $objp->subprice;
				$line->remise_percent = $objp->remise_percent;
				$line->price_ht       = $objp->price_ht;
				$line->price          = (isset($objp->price)?$objp->price:null);	// For backward compatibility
				$line->total_ht       = $objp->total_ht;
				$line->total_tva      = $objp->total_tva;
				$line->total_localtax1= $objp->total_localtax1;
				$line->total_localtax2= $objp->total_localtax2;
				$line->total_ttc      = $objp->total_ttc;
				$line->fk_product     = 0;
				$line->info_bits      = $objp->info_bits;

				$line->fk_user_author   = $objp->fk_user_author;
				$line->fk_user_ouverture= $objp->fk_user_ouverture;
				$line->fk_user_cloture  = $objp->fk_user_cloture;

				$line->description    = $objp->description;

				$line->date_ouverture_prevue = $this->db->jdate($objp->date_ouverture_prevue);
				$line->date_ouverture        = $this->db->jdate($objp->date_ouverture);
				$line->date_fin_validite     = $this->db->jdate($objp->date_fin_validite);
				$line->date_cloture          = $this->db->jdate($objp->date_cloture);
				// For backward compatibility
				$line->date_debut_prevue = $this->db->jdate($objp->date_ouverture_prevue);
				$line->date_debut_reel   = $this->db->jdate($objp->date_ouverture);
				$line->date_fin_prevue   = $this->db->jdate($objp->date_fin_validite);
				$line->date_fin_reel     = $this->db->jdate($objp->date_cloture);
				$line->fk_unit        = $objp->fk_unit;

				if ($line->statut == 0) $this->nbofserviceswait++;
				if ($line->statut == 4 && (empty($line->date_fin_prevue) || $line->date_fin_prevue >= $now)) $this->nbofservicesopened++;
				if ($line->statut == 4 && (! empty($line->date_fin_prevue) && $line->date_fin_prevue < $now)) $this->nbofservicesexpired++;
				if ($line->statut == 5) $this->nbofservicesclosed++;


				// Retreive all extrafield for propal
				// fetch optionals attributes and labels

				$line->fetch_optionals($line->id,$extralabelsline);


				$this->lines[]        = $line;

				$total_ttc+=$objp->total_ttc;
				$total_vat+=$objp->total_tva;
				$total_ht+=$objp->total_ht;

				$i++;
			}

			$this->db->free($result);
		}
		else
		{
			dol_syslog(get_class($this)."::Fetch Erreur lecture des lignes de contrat non liees aux produits");
			$this->error=$this->db->error();
			return -2;
		}

		$this->nbofservices=count($this->lines);
		$this->total_ttc = price2num($total_ttc);   // TODO For the moment value is false as value is not stored in database for line linked to products
		$this->total_vat = price2num($total_vat);   // TODO For the moment value is false as value is not stored in database for line linked to products
		$this->total_ht = price2num($total_ht);     // TODO For the moment value is false as value is not stored in database for line linked to products
		
		return $this->lines;
	}

}

class Contratligneext extends ContratLigne
{
	var $aData;
	function get_sum_taxes($id)
	{
		if (empty($id)) return -1;

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
		$sql.= " t.fk_unit,";
		$sql.= " t.multicurrency_total_ht,";
		$sql.= " t.multicurrency_total_tva,";
		$sql.= " t.multicurrency_subprice,";
		$sql.= " t.multicurrency_total_ttc";

		$sql.= " FROM ".MAIN_DB_PREFIX."contratdet as t LEFT JOIN ".MAIN_DB_PREFIX."product as p ON p.rowid = t.fk_product";
		if ($id)  $sql.= " WHERE t.fk_contrat = ".$id;


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
	 *  Mets a jour une ligne de contrat
	 *
	 *  @param	int			$rowid            	Id de la ligne de facture
	 *  @param  string		$desc             	Description de la ligne
	 *  @param  float		$pu               	Prix unitaire
	 *  @param  int			$qty              	Quantite
	 *  @param  float		$remise_percent   	Pourcentage de remise de la ligne
	 *  @param  int			$date_start       	Date de debut prevue
	 *  @param  int			$date_end         	Date de fin prevue
	 *  @param  float		$tvatx            	Taux TVA
	 *  @param  float		$localtax1tx      	Local tax 1 rate
	 *  @param  float		$localtax2tx      	Local tax 2 rate
	 *  @param  int|string	$date_debut_reel  	Date de debut reelle
	 *  @param  int|string	$date_fin_reel    	Date de fin reelle
	 *	@param	string		$price_base_type	HT or TTC
	 * 	@param  int			$info_bits			Bits de type de lignes
	 * 	@param  int			$fk_fournprice		Fourn price id
	 *  @param  int			$pa_ht				Buying price HT
	 *  @param	array		$array_options		extrafields array
	 * 	@param 	string		$fk_unit 			Code of the unit to use. Null to use the default one
	 *  @return int              				< 0 si erreur, > 0 si ok
	 */
	function updatelineadd($rowid, $desc, $pu, $qty, $remise_percent, $date_start, $date_end, $tvatx, $localtax1tx=0.0, $localtax2tx=0.0, $date_debut_reel='', $date_fin_reel='', $price_base_type='HT', $info_bits=0, $fk_fournprice=null, $pa_ht = 0,$array_options=0, $fk_unit = null,$lines)
	{
		global $user, $conf, $langs, $mysoc;

		// Nettoyage parametres
		$qty=trim($qty);
		$desc=trim($desc);
		$desc=trim($desc);
		$price = price2num($pu);
		$tvatx = price2num($tvatx);
		$localtax1tx = price2num($localtax1tx);
		$localtax2tx = price2num($localtax2tx);
		$pa_ht=price2num($pa_ht);

		$subprice = $price;
		$remise = 0;
		if (dol_strlen($remise_percent) > 0)
		{
			$remise = round(($pu * $remise_percent / 100), 2);
			$price = $pu - $remise;
		}
		else
		{
			$remise_percent=0;
		}

		dol_syslog(get_class($this)."::updateline $rowid, $desc, $pu, $qty, $remise_percent, $date_start, $date_end, $date_debut_reel, $date_fin_reel, $tvatx, $localtax1tx, $localtax2tx, $price_base_type, $info_bits");

		$this->db->begin();

		// Calcul du total TTC et de la TVA pour la ligne a partir de
		// qty, pu, remise_percent et tvatx
		// TRES IMPORTANT: C'est au moment de l'insertion ligne qu'on doit stocker
		// la part ht, tva et ttc, et ce au niveau de la ligne qui a son propre taux tva.

		$localtaxes_type=getLocalTaxesFromRate($tvatx, 0, $this->societe, $mysoc);
		$tvatx = preg_replace('/\s*\(.*\)/','',$tvatx);  // Remove code into vatrate.
		
		$tabprice=calcul_price_total($qty, $pu, $remise_percent, $tvatx, $localtax1tx, $localtax2tx, 0, $price_base_type, $info_bits, 1, $mysoc, $localtaxes_type);
		$total_ht  = $tabprice[0];
		$total_tva = $tabprice[1];
		$total_ttc = $tabprice[2];
		$total_localtax1= $tabprice[9];
		$total_localtax2= $tabprice[10];

		$localtax1_type=$localtaxes_type[0];
		$localtax2_type=$localtaxes_type[2];

		$total_ht  = $lines->total_ht;
		$total_tva = $lines->total_tva;
		$total_ttc = $lines->total_ttc;
		$total_localtax1= $lines->total_localtax1;
		$total_localtax2= $lines->total_localtax2;

		$localtax1_type=$lines->localtax1_type;
		$localtax2_type=$lines->localtax2_type;

		// TODO A virer
		// Anciens indicateurs: $price, $remise (a ne plus utiliser)
		$remise = $lines->remise;
		$remise_percent = $lines->remise_percent;
		$price = price2num(round($pu, 2));
		//if (dol_strlen($remise_percent) > 0)
		//{
		//    $remise = round(($pu * $remise_percent / 100), 2);
		//    $price = $pu - $remise;
		//}

	    if (empty($pa_ht)) $pa_ht=0;

		// if buy price not defined, define buyprice as configured in margin admin
		if ($this->pa_ht == 0) 
		{
			if (($result = $this->defineBuyPrice($pu_ht, $remise_percent)) < 0)
			{
				return $result;
			}
			else
			{
				$pa_ht = $result;
			}
		}

		$sql = "UPDATE ".MAIN_DB_PREFIX."contratdet set description='".$this->db->escape($desc)."'";
		$sql.= ",price_ht='" .     price2num($price)."'";
		$sql.= ",subprice='" .     price2num($subprice)."'";
		$sql.= ",remise='" .       price2num($remise)."'";
		$sql.= ",remise_percent='".price2num($remise_percent)."'";
		$sql.= ",remise='".price2num($remise)."'";
		$sql.= ",qty='".$qty."'";
		$sql.= ",tva_tx='".        price2num($tvatx)."'";
		$sql.= ",localtax1_tx='".  price2num($localtax1tx)."'";
		$sql.= ",localtax2_tx='".  price2num($localtax2tx)."'";
		$sql.= ",localtax1_type='".$localtax1_type."'";
		$sql.= ",localtax2_type='".$localtax2_type."'";
		$sql.= ", total_ht='".     price2num($total_ht)."'";
		$sql.= ", total_tva='".    price2num($total_tva)."'";
		$sql.= ", total_localtax1='".price2num($total_localtax1)."'";
		$sql.= ", total_localtax2='".price2num($total_localtax2)."'";
		$sql.= ", total_ttc='".      price2num($total_ttc)."'";
		$sql.= ", fk_product_fournisseur_price='".$fk_fournprice."'";
		$sql.= ", buy_price_ht='".price2num($pa_ht)."'";
		if ($date_start > 0) { $sql.= ",date_ouverture_prevue='".$this->db->idate($date_start)."'"; }
		else { $sql.=",date_ouverture_prevue=null"; }
		if ($date_end > 0) { $sql.= ",date_fin_validite='".$this->db->idate($date_end)."'"; }
		else { $sql.=",date_fin_validite=null"; }
		if ($date_debut_reel > 0) { $sql.= ",date_ouverture='".$this->db->idate($date_debut_reel)."'"; }
		else { $sql.=",date_ouverture=null"; }
		if ($date_fin_reel > 0) { $sql.= ",date_cloture='".$this->db->idate($date_fin_reel)."'"; }
		else { $sql.=",date_cloture=null"; }
		$sql .= ", fk_unit=".($fk_unit?"'".$this->db->escape($fk_unit)."'":"null");
		$sql .= " WHERE rowid = ".$rowid;

		dol_syslog(get_class($this)."::updatelineadd", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result)
		{
			$result=$this->update_statut($user);
			if ($result >= 0)
			{

				if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED) && is_array($array_options) && count($array_options)>0) // For avoid conflicts if trigger used
				{
					$contractline = new ContratLigne($this->db);
					$contractline->array_options=$array_option;
					$contractline->id= $this->db->last_insert_id(MAIN_DB_PREFIX.$contractline->table_element);
					$result=$contractline->insertExtraFields();
					if ($result < 0)
					{
						$this->error[]=$contractline->error;
						$error++;
					}
				}

				if (empty($error)) {
			        // Call trigger
			        $result=$this->call_trigger('LINECONTRACT_UPDATE',$user);
			        if ($result < 0)
			        {
			            $this->db->rollback();
			            return -3;
			        }
			        // End call triggers

					$this->db->commit();
					return 1;
				}
			}
			else
			{
				$this->db->rollback();
				dol_syslog(get_class($this)."::updateligneadd Erreur -2");
				return -2;
			}
		}
		else
		{
			$this->db->rollback();
			$this->error=$this->db->error();
			dol_syslog(get_class($this)."::updateligneadd Erreur -1");
			return -1;
		}
	}

	/**
	 *      Update database for contract line
	 *
	 *      @param	User	$user        	User that modify
	 *      @param  int		$notrigger	    0=no, 1=yes (no update trigger)
	 *      @return int         			<0 if KO, >0 if OK
	 */
	function updateadd($user, $notrigger=0)
	{
		global $conf, $langs, $mysoc;

		$error=0;

		// Clean parameters
		$this->fk_contrat=trim($this->fk_contrat);
		$this->fk_product=trim($this->fk_product);
		$this->statut=(int) $this->statut;
		$this->label=trim($this->label);
		$this->description=trim($this->description);
		$this->tva_tx=trim($this->tva_tx);
		$this->localtax1_tx=trim($this->localtax1_tx);
		$this->localtax2_tx=trim($this->localtax2_tx);
		$this->qty=trim($this->qty);
		$this->remise_percent=trim($this->remise_percent);
		$this->remise=trim($this->remise);
		$this->fk_remise_except=trim($this->fk_remise_except);
		$this->subprice=price2num($this->subprice);
		$this->price_ht=price2num($this->price_ht);
		$this->total_ht=trim($this->total_ht);
		$this->total_tva=trim($this->total_tva);
		$this->total_localtax1=trim($this->total_localtax1);
		$this->total_localtax2=trim($this->total_localtax2);
		$this->total_ttc=trim($this->total_ttc);
		$this->info_bits=trim($this->info_bits);
		$this->fk_user_author=trim($this->fk_user_author);
		$this->fk_user_ouverture=trim($this->fk_user_ouverture);
		$this->fk_user_cloture=trim($this->fk_user_cloture);
		$this->commentaire=trim($this->commentaire);
		//if (empty($this->subprice)) $this->subprice = 0;
		if (empty($this->price_ht)) $this->price_ht = 0;
		if (empty($this->total_ht)) $this->total_ht = 0;
		if (empty($this->total_tva)) $this->total_tva = 0;
		if (empty($this->total_ttc)) $this->total_ttc = 0;

		// Check parameters
		// Put here code to add control on parameters values

		// Calcul du total TTC et de la TVA pour la ligne a partir de
		// qty, pu, remise_percent et txtva
		// TRES IMPORTANT: C'est au moment de l'insertion ligne qu'on doit stocker
		// la part ht, tva et ttc, et ce au niveau de la ligne qui a son propre taux tva.
		//$localtaxes_type = getLocalTaxesFromRate($this->txtva, 0, $this->societe, $mysoc);

		//$tabprice=calcul_price_total($this->qty, $this->price_ht, $this->remise_percent, $this->tva_tx, $this->localtax1_tx, $this->localtax2_tx, 0, 'HT', 0, 1, $mysoc, $localtaxes_type);
		//$this->total_ht  = $tabprice[0];
		//$this->total_tva = $tabprice[1];
		//$this->total_ttc = $tabprice[2];
		//$this->total_localtax1= $tabprice[9];
		//$this->total_localtax2= $tabprice[10];


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

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."contratdet SET";
		$sql.= " fk_contrat='".$this->fk_contrat."',";
		$sql.= " fk_product=".($this->fk_product?"'".$this->fk_product."'":'null').",";
		$sql.= " statut='".$this->statut."',";
		$sql.= " label='".$this->db->escape($this->label)."',";
		$sql.= " description='".$this->db->escape($this->description)."',";
		$sql.= " date_commande=".($this->date_commande!=''?"'".$this->db->idate($this->date_commande)."'":"null").",";
		$sql.= " date_ouverture_prevue=".($this->date_ouverture_prevue!=''?"'".$this->db->idate($this->date_ouverture_prevue)."'":"null").",";
		$sql.= " date_ouverture=".($this->date_ouverture!=''?"'".$this->db->idate($this->date_ouverture)."'":"null").",";
		$sql.= " date_fin_validite=".($this->date_fin_validite!=''?"'".$this->db->idate($this->date_fin_validite)."'":"null").",";
		$sql.= " date_cloture=".($this->date_cloture!=''?"'".$this->db->idate($this->date_cloture)."'":"null").",";
		$sql.= " tva_tx='".$this->tva_tx."',";
		$sql.= " localtax1_tx='".$this->localtax1_tx."',";
		$sql.= " localtax2_tx='".$this->localtax2_tx."',";
		$sql.= " qty='".$this->qty."',";
		$sql.= " remise_percent='".$this->remise_percent."',";
		$sql.= " remise=".($this->remise?"'".$this->remise."'":"null").",";
		$sql.= " fk_remise_except=".($this->fk_remise_except?"'".$this->fk_remise_except."'":"null").",";
		$sql.= " subprice=".($this->subprice != '' ? $this->subprice : "null").",";
		$sql.= " price_ht=".($this->price_ht != '' ? $this->price_ht : "null").",";
		$sql.= " total_ht='".$this->total_ht."',";
		$sql.= " total_tva='".$this->total_tva."',";
		$sql.= " total_localtax1='".$this->total_localtax1."',";
		$sql.= " total_localtax2='".$this->total_localtax2."',";
		$sql.= " total_ttc='".$this->total_ttc."',";
		$sql.= " fk_product_fournisseur_price=".(!empty($this->fk_fournprice)?$this->fk_fournprice:"NULL").",";
		$sql.= " buy_price_ht='".price2num($this->pa_ht)."',";
		$sql.= " info_bits='".$this->info_bits."',";
		$sql.= " fk_user_author=".($this->fk_user_author >= 0?$this->fk_user_author:"NULL").",";
		$sql.= " fk_user_ouverture=".($this->fk_user_ouverture > 0?$this->fk_user_ouverture:"NULL").",";
		$sql.= " fk_user_cloture=".($this->fk_user_cloture > 0?$this->fk_user_cloture:"NULL").",";
		$sql.= " commentaire='".$this->db->escape($this->commentaire)."'";
		$sql.= ", fk_unit=".(!$this->fk_unit ? 'NULL' : $this->fk_unit);
		$sql.= " WHERE rowid=".$this->id;

		dol_syslog(get_class($this)."::update", LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql)
		{
			$contrat=new Contrat($this->db);
			$contrat->fetch($this->fk_contrat);
			$result=$contrat->update_statut($user);
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			$error++;
			//return -1;
		}

		if (empty($conf->global->MAIN_EXTRAFIELDS_DISABLED) && is_array($this->array_options) && count($this->array_options)>0) // For avoid conflicts if trigger used
		{

			$result=$this->insertExtraFields();
			if ($result < 0)
			{
				$error++;
			}
		}

		if (empty($error)) {
		if (! $notrigger)
		{
            // Call trigger
            $result=$this->call_trigger('LINECONTRACT_UPDATE',$user);
            if ($result < 0) { $error++; $this->db->rollback(); return -1; }
            // End call triggers
		}
		}

		if (empty($error)) {
        $this->db->commit();
		return 1;
		} else {
			$this->db->rollback();
			$this->errors[]=$this->error;
			return -1;
		}
	}
}
?>