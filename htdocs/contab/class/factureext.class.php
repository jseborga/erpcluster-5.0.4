<?php
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';

class Factureext extends Facture
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
		dol_syslog(__METHOD__, LOG_DEBUG);
		global $conf;

		if (empty($rowid) && empty($ref) && empty($ref_ext) && empty($ref_int)) return -1;

		$sql = 'SELECT f.rowid,f.facnumber,f.ref_client,f.ref_ext,f.ref_int,f.type,f.fk_soc,f.amount';
		$sql.= ', f.tva, f.localtax1, f.localtax2, f.total, f.total_ttc, f.revenuestamp';
		$sql.= ', f.remise_percent, f.remise_absolue, f.remise';
		$sql.= ', f.datef as df, f.date_pointoftax';
		$sql.= ', f.date_lim_reglement as dlr';
		$sql.= ', f.datec as datec';
		$sql.= ', f.date_valid as datev';
		$sql.= ', f.tms as datem';
		$sql.= ', f.note_private, f.note_public, f.fk_statut, f.paye, f.close_code, f.close_note, f.fk_user_author, f.fk_user_valid, f.model_pdf';
		$sql.= ', f.fk_facture_source';
		$sql.= ', f.fk_mode_reglement, f.fk_cond_reglement, f.fk_projet, f.extraparams';
		$sql.= ', f.situation_cycle_ref, f.situation_counter, f.situation_final';
		$sql.= ', f.fk_account';
		$sql.= ", f.fk_multicurrency, f.multicurrency_code, f.multicurrency_tx, f.multicurrency_total_ht, f.multicurrency_total_tva, f.multicurrency_total_ttc";
		$sql.= ', p.code as mode_reglement_code, p.libelle as mode_reglement_libelle';
		$sql.= ', c.code as cond_reglement_code, c.libelle as cond_reglement_libelle, c.libelle_facture as cond_reglement_libelle_doc';
		$sql.= ', f.fk_incoterms, f.location_incoterms';
		$sql.= ", i.libelle as libelle_incoterms";
		$sql.= ' FROM '.MAIN_DB_PREFIX.'facture as f';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_payment_term as c ON f.fk_cond_reglement = c.rowid';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_paiement as p ON f.fk_mode_reglement = p.id';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_incoterms as i ON f.fk_incoterms = i.rowid';

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		$sql.= ' WHERE f.entity = '.$conf->entity;
		if (! empty($conf->multicompany->enabled)) {
			$sql .= " AND entity IN (" . getEntity("contabtransaction", 1) . ")";
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' AND ' . implode(' '.$filtermode.' ', $sqlwhere);
		}
		if ($filterstatic){
			$sql.= $filterstatic;
		}
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
				$line = new FactureLine();

				$line->id                   = $obj->rowid;
				$line->ref                  = $obj->facnumber;
				$line->ref_client           = $obj->ref_client;
				$line->ref_ext              = $obj->ref_ext;
				$line->ref_int              = $obj->ref_int;
				$line->type                 = $obj->type;
				$line->date                 = $this->db->jdate($obj->df);
				$line->date_pointoftax      = $this->db->jdate($obj->date_pointoftax);
				$line->date_creation        = $this->db->jdate($obj->datec);
				$line->date_validation      = $this->db->jdate($obj->datev);
				$line->datem                = $this->db->jdate($obj->datem);
				$line->remise_percent       = $obj->remise_percent;
				$line->remise_absolue       = $obj->remise_absolue;
				$line->total_ht             = $obj->total;
				$line->total_tva            = $obj->tva;
				$line->total_localtax1      = $obj->localtax1;
				$line->total_localtax2      = $obj->localtax2;
				$line->total_ttc            = $obj->total_ttc;
				$line->revenuestamp         = $obj->revenuestamp;
				$line->paye                 = $obj->paye;
				$line->close_code           = $obj->close_code;
				$line->close_note           = $obj->close_note;
				$line->socid                = $obj->fk_soc;
				$line->statut               = $obj->fk_statut;
				$line->date_lim_reglement   = $this->db->jdate($obj->dlr);
				$line->mode_reglement_id    = $obj->fk_mode_reglement;
				$line->mode_reglement_code  = $obj->mode_reglement_code;
				$line->mode_reglement       = $obj->mode_reglement_libelle;
				$line->cond_reglement_id    = $obj->fk_cond_reglement;
				$line->cond_reglement_code  = $obj->cond_reglement_code;
				$line->cond_reglement       = $obj->cond_reglement_libelle;
				$line->cond_reglement_doc   = $obj->cond_reglement_libelle_doc;
				$line->fk_account           = ($obj->fk_account>0)?$obj->fk_account:null;
				$line->fk_project           = $obj->fk_projet;
				$line->fk_facture_source    = $obj->fk_facture_source;
				$line->note                 = $obj->note_private;   // deprecated
				$line->note_private         = $obj->note_private;
				$line->note_public          = $obj->note_public;
				$line->user_author          = $obj->fk_user_author;
				$line->user_valid           = $obj->fk_user_valid;
				$line->modelpdf             = $obj->model_pdf;
				$line->situation_cycle_ref  = $obj->situation_cycle_ref;
				$line->situation_counter    = $obj->situation_counter;
				$line->situation_final      = $obj->situation_final;
				$line->extraparams          = (array) json_decode($obj->extraparams, true);

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

				if ($line->type == self::TYPE_SITUATION && $fetch_situation)
					{
						$line->fetchPreviousNextSituationInvoice();
					}

					if ($line->statut == self::STATUS_DRAFT)    $this->brouillon = 1;

				// Retrieve all extrafield for invoice
				// fetch optionals attributes and labels
						require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
						$extrafields=new ExtraFields($this->db);
						$extralabels=$extrafields->fetch_name_optionals_label($this->table_element,true);
						$this->fetch_optionals($this->id,$extralabels);

				//lines //buscamos el detalle
				$objtmp = new Facture($this->db);
				$objtmp->fetch($line->id);
				$line->lines  = array();

				$result=$objtmp->fetch_lines();
				if ($result < 0)
				{
					$this->error=$this->db->error();
					return -3;
				}
				else
					$line->lines = $objtmp->lines;
				$this->lines[] = $line;
			}
			return $num;
		}
		else
		{
			$this->error='Bill with id '.$rowid.' or ref '.$ref.' not found';
			dol_syslog(get_class($this)."::fetch Error ".$this->error, LOG_ERR);
			return -1;
		}
	}
}

class FactureLine
{
	var $id;
	var $ref;
	var $ref_client;
	var $ref_ext;
	var $ref_int;
	var $type;
	var $date;
	var $date_pointoftax;
	var $date_creation;
	var $date_validation;
	var $datem;
	var $remise_percent;
	var $remise_absolue;
	var $total_ht;
	var $total_tva;
	var $total_localtax1;
	var $total_localtax2;
	var $total_ttc;
	var $revenuestamp;
	var $paye;
	var $close_code;
	var $close_note;
	var $socid;
	var $statut;
	var $date_lim_reglement;
	var $mode_reglement_id;
	var $mode_reglement_code;
	var $mode_reglement;
	var $cond_reglement_id;
	var $cond_reglement_code;
	var $cond_reglement;
	var $cond_reglement_doc;
	var $fk_account;
	var $fk_project;
	var $fk_facture_source;
	var $note;
	var $note_private;
	var $note_public;
	var $user_author;
	var $user_valid;
	var $modelpdf;
	var $situation_cycle_ref;
	var $situation_counter;
	var $situation_final;
	var $extraparams;

				//Incoterms
	var $fk_incoterms;
	var $location_incoterms;
	var $libelle_incoterms;

				// Multicurrency
	var $fk_multicurrency;
	var $multicurrency_code;
	var $multicurrency_tx;
	var $multicurrency_total_ht;
	var $multicurrency_total_tva;
	var $multicurrency_total_ttc;
}
?>