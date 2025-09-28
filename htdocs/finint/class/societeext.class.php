<?php
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

class Societeext extends Societe
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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	{
		global $conf,$langs,$user;
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT t.rowid, t.nom as name, t.name_alias, t.entity, t.ref_ext, t.ref_int, t.address, t.datec as date_creation, t.prefix_comm';
		$sql .= ', t.status';
		$sql .= ', t.price_level';
		$sql .= ', t.tms as date_modification';
		$sql .= ', t.phone, t.fax, t.email, t.skype, t.url, t.zip, t.town, t.note_private, t.note_public, t.model_pdf, t.client, t.fournisseur';
		$sql .= ', t.siren as idprof1, t.siret as idprof2, t.ape as idprof3, t.idprof4, t.idprof5, t.idprof6';
		$sql .= ', t.capital, t.tva_intra';
		$sql .= ', t.fk_typent as typent_id';
		$sql .= ', t.fk_effectif as effectif_id';
		$sql .= ', t.fk_forme_juridique as forme_juridique_code';
		$sql .= ', t.webservices_url, t.webservices_key';
		$sql .= ', t.code_client, t.code_fournisseur, t.code_compta, t.code_compta_fournisseur, t.parent, t.barcode';
		$sql .= ', t.fk_departement, t.fk_pays as country_id, t.fk_stcomm, t.remise_client, t.mode_reglement, t.cond_reglement, t.tva_assuj';
		$sql .= ', t.mode_reglement_supplier, t.cond_reglement_supplier, t.localtax1_assuj, t.localtax1_value, t.localtax2_assuj, t.localtax2_value, t.fk_prospectlevel, t.default_lang, t.logo';
		$sql .= ', t.fk_shipping_method';
		$sql .= ', t.outstanding_limit, t.import_key, t.canvas, t.fk_incoterms, t.location_incoterms';
		$sql .= ', t.fk_multicurrency, t.multicurrency_code';
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) 
		{
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

		$resql = $this->db->query($sql);
		if ($resql) 
		{
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) 
			{
				$line = new Societe($this->db);

				$line->id           = $obj->rowid;
				$line->entity       = $obj->entity;
				$line->canvas		= $obj->canvas;

				$line->ref          = $obj->rowid;
				$line->name 		= $obj->name;
				$line->nom          = $obj->name;
						// deprecated
				$line->name_alias = $obj->name_alias;
				$line->ref_ext      = $obj->ref_ext;
				$line->ref_int      = $obj->ref_int;

				$line->date_creation = $this->db->jdate($obj->date_creation);
				$line->date_modification = $this->db->jdate($obj->date_modification);

				$line->address 		= $obj->address;
				$line->zip 			= $obj->zip;
				$line->town 		= $obj->town;

				$line->country_id   = $obj->country_id;
				$line->country_code = $obj->country_id?$obj->country_code:'';
				$line->country 		= $obj->country_id?($langs->trans('Country'.$obj->country_code)!='Country'.$obj->country_code?$langs->transnoentities('Country'.$obj->country_code):$obj->country):'';

				$line->state_id     = $obj->fk_departement;
				$line->state_code   = $obj->state_code;
				$line->state        = ($obj->state!='-'?$obj->state:'');

				$transcode=$langs->trans('StatusProspect'.$obj->fk_stcomm);
				$libelle=($transcode!='StatusProspect'.$obj->fk_stcomm?$transcode:$obj->stcomm);
				$line->stcomm_id = $obj->fk_stcomm;     // id statut commercial
				$line->statut_commercial = $libelle;    // libelle statut commercial

				$line->email = $obj->email;
				$line->skype = $obj->skype;
				$line->url = $obj->url;
				$line->phone = $obj->phone;
				$line->fax = $obj->fax;

				$line->parent    = $obj->parent;

				$line->idprof1		= $obj->idprof1;
				$line->idprof2		= $obj->idprof2;
				$line->idprof3		= $obj->idprof3;
				$line->idprof4		= $obj->idprof4;
				$line->idprof5		= $obj->idprof5;
				$line->idprof6		= $obj->idprof6;

				$line->capital   = $obj->capital;

				$line->code_client = $obj->code_client;
				$line->code_fournisseur = $obj->code_fournisseur;

				$line->code_compta = $obj->code_compta;
				$line->code_compta_fournisseur = $obj->code_compta_fournisseur;

				$line->barcode = $obj->barcode;

				$line->tva_assuj      = $obj->tva_assuj;
				$line->tva_intra      = $obj->tva_intra;
				$line->status = $obj->status;

				// Local Taxes
				$line->localtax1_assuj      = $obj->localtax1_assuj;
				$line->localtax2_assuj      = $obj->localtax2_assuj;

				$line->localtax1_value		= $obj->localtax1_value;
				$line->localtax2_value		= $obj->localtax2_value;

				$line->typent_id      = $obj->typent_id;
				$line->typent_code    = $obj->typent_code;

				$line->effectif_id    = $obj->effectif_id;
				$line->effectif       = $obj->effectif_id?$obj->effectif:'';

				$line->forme_juridique_code= $obj->forme_juridique_code;
				$line->forme_juridique     = $obj->forme_juridique_code?$obj->forme_juridique:'';

				$line->fk_prospectlevel = $obj->fk_prospectlevel;

				$line->prefix_comm = $obj->prefix_comm;

				$line->remise_percent		= $obj->remise_client;
				$line->mode_reglement_id 	= $obj->mode_reglement;
				$line->cond_reglement_id 	= $obj->cond_reglement;
				$line->mode_reglement_supplier_id 	= $obj->mode_reglement_supplier;
				$line->cond_reglement_supplier_id 	= $obj->cond_reglement_supplier;
				$line->shipping_method_id   = ($obj->fk_shipping_method>0)?$obj->fk_shipping_method:null;

				$line->client      = $obj->client;
				$line->fournisseur = $obj->fournisseur;

				$line->note = $obj->note_private; // TODO Deprecated for backward comtability
				$line->note_private = $obj->note_private;
				$line->note_public = $obj->note_public;
				$line->modelpdf = $obj->model_pdf;
				$line->default_lang = $obj->default_lang;
				$line->logo = $obj->logo;

				$line->webservices_url = $obj->webservices_url;
				$line->webservices_key = $obj->webservices_key;

				$line->outstanding_limit		= $obj->outstanding_limit;

				// multiprix
				$line->price_level = $obj->price_level;

				$line->import_key = $obj->import_key;

				//Incoterms
				$line->fk_incoterms = $obj->fk_incoterms;
				$line->location_incoterms = $obj->location_incoterms;
				$line->libelle_incoterms = $obj->libelle_incoterms;

				// multicurrency
				$line->fk_multicurrency = $obj->fk_multicurrency;
				$line->multicurrency_code = $obj->multicurrency_code;

				if ($lView) $this->fetch($obj->rowid);

				$this->lines[$line->id] = $line;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}
}
?>