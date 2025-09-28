<?php

require_once(DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php');
class Contratext extends Contrat
{

	public $linec;
	public $linesdet;

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

	/**
	 *	Return clicable name (with picto eventually)
	 *
	 *	@param	int		$withpicto		0=No picto, 1=Include picto into link, 2=Only picto
	 *	@param	int		$maxlength		Max length of ref
	 *	@return	string					Chaine avec URL
	 */
	function getNomUrladd($withpicto=0,$maxlength=0,$link='')
	{
		global $langs;

		$result='';
		$label=$langs->trans("ShowContract").': '.$this->ref;

		$link = '<a href="'.DOL_URL_ROOT.'/monprojet/contrat.php?'.$link.'" title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip">';
		$linkend='</a>';

		$picto='contract';


		if ($withpicto) $result.=($link.img_object($label, $picto, 'class="classfortooltip"').$linkend);
		if ($withpicto && $withpicto != 2) $result.=' ';
		if ($withpicto != 2) $result.=$link.($maxlength?dol_trunc($this->ref,$maxlength):$this->ref).$linkend;
		return $result;
	}
}

class ContratLigneext extends ContratLigne
{
	/**
	 *	Returns the text label from units dictionary
	 *
	 * 	@param	string $type Label type (long or short)
	 *	@return	string|int <0 if ko, label if ok
	 */
	function getLabelOfUnit($type='long')
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
			$label = $res[$label_type];
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
}

//class ContratLigneextext extends ContratLigne
//{

//}

?>