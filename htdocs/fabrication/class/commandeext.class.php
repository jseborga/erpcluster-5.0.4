<?php

require_once DOL_DOCUMENT_ROOT.'/commande/class/commande.class.php';
class Commandeext extends Commande
{

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
		$sql = 'SELECT c.rowid, c.date_creation, c.ref, c.fk_soc, c.fk_user_author, c.fk_user_valid, c.fk_statut';
		$sql.= ', c.amount_ht, c.total_ht, c.total_ttc, c.tva as total_tva, c.localtax1 as total_localtax1, c.localtax2 as total_localtax2, c.fk_cond_reglement, c.fk_mode_reglement, c.fk_availability, c.fk_input_reason';
		$sql.= ', c.fk_account';
		$sql.= ', c.date_commande';
		$sql.= ', c.date_livraison';
		$sql.= ', c.fk_shipping_method';
		$sql.= ', c.fk_warehouse';
		$sql.= ', c.fk_projet, c.remise_percent, c.remise, c.remise_absolue, c.source, c.facture as billed';
		$sql.= ', c.note_private, c.note_public, c.ref_client, c.ref_ext, c.ref_int, c.model_pdf, c.fk_delivery_address, c.extraparams';
		$sql.= ', c.fk_incoterms, c.location_incoterms';
		$sql.= ", c.fk_multicurrency, c.multicurrency_code, c.multicurrency_tx, c.multicurrency_total_ht, c.multicurrency_total_tva, c.multicurrency_total_ttc";
		$sql.= ", i.libelle as libelle_incoterms";
		$sql.= ', p.code as mode_reglement_code, p.libelle as mode_reglement_libelle';
		$sql.= ', cr.code as cond_reglement_code, cr.libelle as cond_reglement_libelle, cr.libelle_facture as cond_reglement_libelle_doc';
		$sql.= ', ca.code as availability_code, ca.label as availability_label';
		$sql.= ', dr.code as demand_reason_code';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'commande as c';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_payment_term as cr ON (c.fk_cond_reglement = cr.rowid)';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_paiement as p ON (c.fk_mode_reglement = p.id)';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_availability as ca ON (c.fk_availability = ca.rowid)';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_input_reason as dr ON (c.fk_input_reason = ca.rowid)';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_incoterms as i ON c.fk_incoterms = i.rowid';

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
			$sql .= " AND entity IN (" . getEntity("conc", 1) . ")";
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
				$line = new Commande($this->db);

				$line->id                   = $obj->rowid;
				$line->ref                  = $obj->ref;
				$line->ref_client           = $obj->ref_client;
				$line->ref_customer         = $obj->ref_client;
				$line->ref_ext              = $obj->ref_ext;
				$line->ref_int              = $obj->ref_int;
				$line->socid                = $obj->fk_soc;
				$line->statut               = $obj->fk_statut;
				$line->user_author_id       = $obj->fk_user_author;
				$line->user_valid           = $obj->fk_user_valid;
				$line->total_ht             = $obj->total_ht;
				$line->total_tva            = $obj->total_tva;
				$line->total_localtax1      = $obj->total_localtax1;
				$line->total_localtax2      = $obj->total_localtax2;
				$line->total_ttc            = $obj->total_ttc;
				$line->date                 = $this->db->jdate($obj->date_commande);
				$line->date_commande        = $this->db->jdate($obj->date_commande);
				$line->remise               = $obj->remise;
				$line->remise_percent       = $obj->remise_percent;
				$line->remise_absolue       = $obj->remise_absolue;
				$line->source               = $obj->source;
				$line->facturee             = $obj->billed;         // deprecated
				$line->billed               = $obj->billed;
				$line->note                 = $obj->note_private;   // deprecated
				$line->note_private         = $obj->note_private;
				$line->note_public          = $obj->note_public;
				$line->fk_project           = $obj->fk_projet;
				$line->modelpdf             = $obj->model_pdf;
				$line->mode_reglement_id    = $obj->fk_mode_reglement;
				$line->mode_reglement_code  = $obj->mode_reglement_code;
				$line->mode_reglement       = $obj->mode_reglement_libelle;
				$line->cond_reglement_id    = $obj->fk_cond_reglement;
				$line->cond_reglement_code  = $obj->cond_reglement_code;
				$line->cond_reglement       = $obj->cond_reglement_libelle;
				$line->cond_reglement_doc   = $obj->cond_reglement_libelle_doc;
				$line->fk_account           = $obj->fk_account;
				$line->availability_id      = $obj->fk_availability;
				$line->availability_code    = $obj->availability_code;
				$line->availability         = $obj->availability_label;
				$line->demand_reason_id     = $obj->fk_input_reason;
				$line->demand_reason_code   = $obj->demand_reason_code;
				$line->date_livraison       = $this->db->jdate($obj->date_livraison);
				$line->shipping_method_id   = ($obj->fk_shipping_method>0)?$obj->fk_shipping_method:null;
				$line->warehouse_id           = ($obj->fk_warehouse>0)?$obj->fk_warehouse:null;
				$line->fk_delivery_address  = $obj->fk_delivery_address;

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

				$line->extraparams          = (array) json_decode($obj->extraparams, true);

				$line->lines                = array();

				if (
					$line->statut == self::STATUS_DRAFT )
				{
					$this->brouillon = 1;
				}

				require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
				$extrafields=new ExtraFields($this->db);
				$extralabels=$extrafields->fetch_name_optionals_label($this->table_element,true);
				$this->fetch_optionals($this->id,$extralabels);

				if ($lView && $num == 1) $this->fetch($obj->rowid);

				//$result=$this->fetch_lines();
				//if ($result < 0)
				//{
				//    return -3;
				//}

				$this->lines[$line->id] = $line;
			}
			$this->db->free($result);

			return $num;
		}
		else
		{
			$this->error=$this->db->error();
			return -1;
		}
	}

    /**
     *	Return clicable link of object (with eventually picto)
     *
     *	@param      int			$withpicto      Add picto into link
     *	@param      int			$option         Where point the link (0=> main card, 1,2 => shipment)
     *	@param      int			$max          	Max length to show
     *	@param      int			$short			???
     *  @param	    int   	    $notooltip		1=Disable tooltip
     *	@return     string          			String with URL
     */
    function getNomUrladd($withpicto=0,$option=0,$max=0,$short=0,$notooltip=0)
    {
        global $conf, $langs, $user;

        if (! empty($conf->dol_no_mouse_hover)) $notooltip=1;   // Force disable tooltips

        $result='';

        if (! empty($conf->expedition->enabled) && ($option == 1 || $option == 2)) $url = DOL_URL_ROOT.'/expedition/shipment.php?id='.$this->id;
        else $url = DOL_URL_ROOT.'/sales/commande/card.php?id='.$this->id;

        if ($short) return $url;

        $picto = 'order';
        $label = '';

		if ($user->rights->commande->lire) {
			$label = '<u>'.$langs->trans("ShowOrder").'</u>';
			$label .= '<br><b>'.$langs->trans('Ref').':</b> '.$this->ref;
			$label .= '<br><b>'.$langs->trans('RefCustomer').':</b> '.($this->ref_customer ? $this->ref_customer : $this->ref_client);
			if (!empty($this->total_ht)) {
				$label .= '<br><b>'.$langs->trans('AmountHT').':</b> '.price($this->total_ht, 0, $langs, 0, -1, -1, $conf->currency);
			}
			if (!empty($this->total_tva)) {
				$label .= '<br><b>'.$langs->trans('VAT').':</b> '.price($this->total_tva, 0, $langs, 0, -1, -1,	$conf->currency);
			}
			if (!empty($this->total_ttc)) {
				$label .= '<br><b>'.$langs->trans('AmountTTC').':</b> '.price($this->total_ttc, 0, $langs, 0, -1, -1, $conf->currency);
			}
		}

		$linkclose='';
		if (empty($notooltip) && $user->rights->commande->lire)
		{
		    if (! empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
		    {
		        $label=$langs->trans("ShowOrder");
		        $linkclose.=' alt="'.dol_escape_htmltag($label, 1).'"';
		    }
		    $linkclose.= ' title="'.dol_escape_htmltag($label, 1).'"';
		    $linkclose.=' class="classfortooltip"';
		}

        $linkstart = '<a href="'.$url.'"';
        $linkstart.=$linkclose.'>';
        $linkend='</a>';

        if ($withpicto) $result.=($linkstart.img_object(($notooltip?'':$label), $picto, ($notooltip?'':'class="classfortooltip"'), 0, 0, $notooltip?0:1).$linkend);
        if ($withpicto && $withpicto != 2) $result.=' ';
        $result.=$linkstart.$this->ref.$linkend;
        return $result;
    }
}
?>