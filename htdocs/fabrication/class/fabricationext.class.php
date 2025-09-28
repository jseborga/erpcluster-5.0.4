<?php
require_once DOL_DOCUMENT_ROOT.'/fabrication/class/fabrication.class.php';

class Fabricationext extends Fabrication
{
		/**
	 *  Create a document onto disk according to template module.
	 *
	 *  @param	    string		$modele			Force template to use ('' to not force)
	 *  @param		Translate	$outputlangs	objet lang a utiliser pour traduction
	 *  @param      int			$hidedetails    Hide details of lines
	 *  @param      int			$hidedesc       Hide description
	 *  @param      int			$hideref        Hide ref
	 *  @return     int         				0 if KO, 1 if OK
	 */
		public function generateDocument($modele, $outputlangs, $hidedetails=0, $hidedesc=0, $hideref=0)
		{
			global $conf,$langs;

			$langs->load("orders");

		// Positionne le modele sur le nom du modele a utiliser
			if (! dol_strlen($modele))
			{
				if (! empty($conf->global->COMMANDE_ADDON_PDF))
				{
					$modele = $conf->global->COMMANDE_ADDON_PDF;
				}
				else
				{
					$modele = 'einstein';
				}
			}

			$modelpath = "fabrication/core/modules/doc/";

			return $this->commonGenerateDocument($modelpath, $modele, $outputlangs, $hidedetails, $hidedesc, $hideref);
		}

	/**
	 *  Returns the reference to the following non used Order depending on the active numbering module
	 *  defined into FABRICATION_ADDON
	 *
	 *  @param	Societe		$soc  	Object thirdparty
	 *  @return string      		Order free reference
	 */
	function getNextNumRef($soc)
	{
		global $db, $langs, $conf;
		$langs->load("fabrication@fabrication");

		$dir = DOL_DOCUMENT_ROOT . "/fabrication/core/modules";

		if (! empty($conf->global->FABRICATION_ADDON))
		{
			$file = $conf->global->FABRICATION_ADDON.".php";
			// Chargement de la classe de numerotation
			$classname = $conf->global->FABRICATION_ADDON;
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
					dol_print_error($db,"Fabrication::getNextNumRef ".$obj->error);
					return "";
				}
			}
			else
			{
				print $langs->trans("Error")." ".$langs->trans("Error_FABRICATION_ADDON_NotDefined");
				return "";
			}
		}
		else
		{
			print $langs->trans("Error")." ".$langs->trans("Error_FABRICATION_ADDON_NotDefined");
			return "";
		}
	}

    /**
     *	Return statut label of Order
     *
     *	@param      int		$mode       0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
     *	@return     string      		Libelle
     */
    function getLibStatut($mode)
    {
    	return $this->LibStatut($this->statut,$mode);
    }

    /**
     *	Return label of statut
     *
     *	@param		int		$statut      	Id statut
     *  @param      int		$facturee    	if invoiced
     *	@param      int		$mode        	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
     *  @return     string					Label of statut
     */
    function LibStatut($statut,$mode)
    {
    	global $langs;
        //print 'x'.$statut.'-'.$facturee;
    	if ($mode == 0)
    	{
    		if ($statut==-1) return $langs->trans('StatusOrderCanceled');
    		if ($statut==0) return $langs->trans('StatusOrderDraft');
    		if ($statut==1) return $langs->trans('StatusOrderValidated');
    		if ($statut==2) return $langs->trans('StatusOrderSentShort');
    		if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderToBill');
    		if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderProcessed');
    	}
    	elseif ($mode == 1)
    	{
    		if ($statut==-1) return $langs->trans('StatusOrderCanceledShort');
    		if ($statut==0) return $langs->trans('StatusOrderDraftShort');
    		if ($statut==1) return $langs->trans('StatusOrderValidatedShort');
    		if ($statut==2) return $langs->trans('StatusOrderSentShort');
    		if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderToBillShort');
    		if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderProcessed');
    	}
    	elseif ($mode == 2)
    	{
    		if ($statut==-1) return img_picto($langs->trans('StatusOrderCanceled'),'statut5').' '.$langs->trans('StatusOrderCanceledShort');
    		if ($statut==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0').' '.$langs->trans('StatusOrderDraftShort');
    		if ($statut==1) return img_picto($langs->trans('StatusOrderValidated'),'statut1').' '.$langs->trans('StatusOrderValidatedShort');
    		if ($statut==2) return img_picto($langs->trans('StatusOrderSent'),'statut3').' '.$langs->trans('StatusOrderSentShort');
    		if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7').' '.$langs->trans('StatusOrderToBillShort');
    		if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6').' '.$langs->trans('StatusOrderProcessedShort');
    	}
    	elseif ($mode == 3)
    	{
    		if ($statut==-1) return img_picto($langs->trans('StatusOrderCanceled'),'statut5');
    		if ($statut==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0');
    		if ($statut==1) return img_picto($langs->trans('StatusOrderValidated'),'statut1');
    		if ($statut==2) return img_picto($langs->trans('StatusOrderSentShort'),'statut3');
    		if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7');
    		if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6');
    	}
    	elseif ($mode == 4)
    	{
        	//echo 'alm '.$facturee;
    		if ($statut==-1) return img_picto($langs->trans('StatusOrderCanceled'),'statut5').' '.$langs->trans('StatusOrderCanceled');
    		if ($statut==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0').' '.$langs->trans('StatusOrderDraft');
            //if ($statut==1) return img_picto($langs->trans('StatusOrderValidated'),'statut1').' '.$langs->trans('StatusOrderValidated');
    		if ($statut==2) return img_picto($langs->trans('StatusOrderSentShort'),'statut3').' '.$langs->trans('StatusOrderSent');
    		if ($statut==1 && (is_null($facturee) || empty($facturee))) return img_picto($langs->trans('StatusOrderPending'),'statut6').' '.$langs->trans('StatusOrderPending');
    		if ($statut==1 && $facturee ==1) return img_picto($langs->trans('Por recibir'),'statut6').' '.$langs->trans('Por recibir');
    		if ($statut==1 && $facturee ==2) return img_picto($langs->trans('StatusOrderProcess'),'statut7').' '.$langs->trans('StatusOrderProcess');

    		if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7').' '.$langs->trans('Entregado a cliente');
    		if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6').' '.$langs->trans('Entregado a CLiente');
    	}
    	elseif ($mode == 5)
    	{
    		if ($statut==-1) return $langs->trans('StatusOrderCanceledShort').' '.img_picto($langs->trans('StatusOrderCanceled'),'statut5');
    		if ($statut==0) return $langs->trans('StatusOrderDraftShort').' '.img_picto($langs->trans('StatusOrderDraft'),'statut0');
    		if ($statut==2) return $langs->trans('StatusOrderSentShort').' '.img_picto($langs->trans('StatusOrderSent'),'statut3');
    		if ($statut==1 && $facturee == -2) return img_picto($langs->trans('StatusOrderPending'),'statut0').' '.$langs->trans('StatusOrderPending');
    		if ($statut==1 && $facturee ==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0').' '.$langs->trans('StatusOrderDraft');
    		if ($statut==1 && $facturee == 1) return img_picto($langs->trans('StatusOrderProcess'),'statut4').' '.$langs->trans('StatusOrderProcess');
    		if ($statut==1 && $facturee ==2) return img_picto($langs->trans('StatusOrderDelivered'),'statut7').' '.$langs->trans('StatusOrderDelivered');
    		if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('Entregado a cliente').' '.img_picto($langs->trans('Entregado a cliente'),'statut7');
    		if ($statut==3 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('Entregado a Cliente').' '.img_picto($langs->trans('Entregado a Cliente'),'statut6');
    	}
    }

    /**
     *  Return combo list of activated countries, into language of user
     *
     *  @param  string  $selected       Id or Code or Label of preselected country
     *  @param  string  $htmlname       Name of html select object
     *  @param  string  $htmloption     Options html on select object
     *  @param  string  $maxlength      Max length for labels (0=no limit)
     *  @return string                  HTML string with select
     */
    function select_fabrication($selected='',$htmlname='fk_fabrication',$htmloption='',$maxlength=0, $showempty=0,$state='')
    {
        global $conf,$langs;

        $langs->load("fabrication@fabrication");

        $out='';
        $countryArray=array();
        $label=array();

        $sql = "SELECT rowid, ref";
        $sql.= " FROM ".MAIN_DB_PREFIX."fabrication as c";
        $sql.= " WHERE c.entity IN (".$conf->entity.")";
        if ($state)
            $sql.= " AND c.statut IN (".$state.")";

        $sql.= ' ORDER BY ref ASC ';

        dol_syslog(get_class($this)."::select_fabrication sql=".$sql);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            $out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
            $num = $this->db->num_rows($resql);
            $i = 0;
            if ($num)
            {
                $foundselected=false;

                while ($i < $num)
                {
                    $obj = $this->db->fetch_object($resql);
                    $countryArray[$i]['rowid']      = $obj->rowid;
                    $countryArray[$i]['ref']    = $obj->ref;
                    $i++;
                }

                //array_multisort($label, SORT_ASC, $countryArray);
                if ($showempty)
                {
                    $out.= '<option value="-1"';
                    if ($selected == -1) $out.= ' selected="selected"';
                    $out.= '>&nbsp;</option>';
                }

                foreach ($countryArray as $row)
                {
                    //print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
                    if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['ref']) )
                    {
                        $foundselected=true;
                        $out.= '<option value="'.$row['rowid'].'" selected="selected">';
                    }
                    else
                    {
                        $out.= '<option value="'.$row['rowid'].'">';
                    }
                    //$out.= dol_trunc($row['ref'],$maxlength,'middle');
                    $out.= $row['ref'];
                    //if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
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
     *  Load all detailed lines into this->lines
     *
     *  @return     int         1 if OK, < 0 if KO
     */
    function fetch_lines()
    {
        $this->lines=array();

        $sql = 'SELECT l.rowid, l.fk_product, l.qty, l.qty_decrease, l.qty_first, l.date_end, l.price, l.price_total, ';
        $sql.= ' p.ref as product_ref, p.fk_product_type as fk_product_type, p.label as product_label, p.description as product_desc';
        $sql.= ' FROM '.MAIN_DB_PREFIX.'fabricationdet as l';
        $sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON l.fk_product = p.rowid';
        $sql.= ' WHERE l.fk_fabrication = '.$this->id;
        $sql.= ' ORDER BY p.ref';
        dol_syslog(get_class($this).'::fetch_lines sql='.$sql, LOG_DEBUG);
        $result = $this->db->query($sql);
        if ($result)
        {
            $num = $this->db->num_rows($result);
            $i = 0;
            while ($i < $num)
            {
                $objp = $this->db->fetch_object($result);
                $line = new Fabricationdet($this->db);

                $line->rowid            = $objp->rowid;
                $line->product_type     = $objp->product_type;      // Type of line
                $line->product_ref      = $objp->product_ref;       // Ref product
                $line->libelle          = $objp->product_label;     // TODO deprecated
                $line->product_label    = $objp->product_label;     // Label product
                $line->product_desc     = $objp->product_desc;      // Description product
                $line->qty              = $objp->qty;
                $line->qty_decrease     = $objp->qty_decrease;
                $line->qty_first        = $objp->qty_first;
                $line->price            = $objp->price;
                $line->price_total      = $objp->price_total;
                $line->fk_product       = $objp->fk_product;
                $line->date_end    = $this->db->jdate($objp->date_end);

                // Ne plus utiliser
                //$line->price            = $objp->price;
                //$line->remise           = $objp->remise;

                $this->lines[$i] = $line;

                $i++;
            }
            $this->db->free($result);
            return 1;
        }
        else
        {
            $this->error=$this->db->error();
            dol_syslog(get_class($this).'::fetch_lines '.$this->error,LOG_ERR);
            return -3;
        }
    }

}
?>