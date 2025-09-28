<?php
require_once DOL_DOCUMENT_ROOT.'/almacen/class/solalmacen.class.php';

class Solalmacenext extends Solalmacen
{
	var $linealm;

	/**
	 *  Returns the reference to the following non used Order depending on the active numbering module
	 *  defined into ALMACEN_ADDON
	 *
	 *  @param  Societe     $soc    Object thirdparty
	 *  @return string              Order free reference
	 */
	function getNextNumRef($soc)
	{
		global $db, $langs, $conf;
		$langs->load("almacen@almacen");

		$dir = DOL_DOCUMENT_ROOT . "/almacen/core/modules";

		if (! empty($conf->global->ALMACEN_ADDON))
		{
			$file = $conf->global->ALMACEN_ADDON.".php";
			// Chargement de la classe de numerotation
			$classname = $conf->global->ALMACEN_ADDON;
			//cambiamos a uno fijo
			$file = 'mod_almacen_ubuntubo.php';
			$classname = 'mod_almacen_ubuntubo';
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
					dol_print_error($db,"Solalmacen::getNextNumRef ".$obj->error);
					return "";
				}
			}
			else
			{
				print $langs->trans("Error")." ".$langs->trans("Error_ALMACEN_ADDON_NotDefined");
				return "";
			}
		}
		else
		{
			print $langs->trans("Error")." ".$langs->trans("Error_ALMACEN_ADDON_NotDefined");
			return "";
		}
	}

	/**
	 *  Load all detailed lines into this->lines
	 *
	 *  @return     int         1 if OK, < 0 if KO
	 */
	function fetch_lines()
	{
		$this->lines=array();

		$sql = 'SELECT l.rowid, l.fk_product, l.qty, l.qty_livree, ';
		$sql.= ' l.date_shipping, l.description, l.fk_entrepot, ';
		$sql.= ' p.ref as product_ref, p.fk_product_type as fk_product_type, p.label as product_label, p.description as product_desc';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'sol_almacendet as l';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'product as p ON l.fk_product = p.rowid';
		$sql.= ' WHERE l.fk_almacen = '.$this->id;
		$sql.= ' ORDER BY p.ref';

		dol_syslog(get_class($this).'::fetch_lines sql='.$sql, LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;
			include_once DOL_DOCUMENT_ROOT.'/product/class/product.class.php';
			while ($i < $num)
			{
				$objp = $this->db->fetch_object($result);
				require_once DOL_DOCUMENT_ROOT.'/almacen/class/solalmacendetext.class.php';
				$line = new Solalmacendetext($this->db);
				$product = new Product($this->db);
				$product->fetch($objp->fk_product);

				$line->rowid            = $objp->rowid;
				$line->product_type     = $objp->product_type;      // Type of line
				$line->product_ref      = $objp->product_ref;       // Ref product
				$line->libelle          = $objp->product_label;     // TODO deprecated
				$line->product_label    = $objp->product_label;     // Label product
				$line->product_desc     = $objp->product_desc;      // Description product
				$line->description     	= $objp->description;      // Description product
				$line->fk_entrepot              = $objp->fk_entrepot;
				$line->qty              = $objp->qty;
				$line->qty_livree       = $objp->qty_livree;
				$line->fk_product       = $objp->fk_product;
				$line->date_shipping    = $this->db->jdate($objp->date_shipping);
				$line->unit = $product->array_options['options_unit'];
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

	/**
	 *  Load object in memory from the database
	 *
	 *  @param  int     $id    Id object
	 *  @return int             <0 if KO, >0 if OK
	 */
	function fetch_fabrication($fk_fabrication)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.entity,";
		$sql.= " t.ref,";
		$sql.= " t.fk_entrepot,";
		$sql.= " t.fk_fabrication,";
		$sql.= " t.fk_user,";
		$sql.= " t.date_creation,";
		$sql.= " t.date_delivery,";
		$sql.= " t.description,";
		$sql.= " t.statut";


		$sql.= " FROM ".MAIN_DB_PREFIX."sol_almacen as t";
		$sql.= " WHERE t.fk_fabrication = ".$fk_fabrication;

		dol_syslog(get_class($this)."::fetch_fabrication sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$i = 0;
				$num = $this->db->num_rows($resql);
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);

					$this->array[$obj->rowid] =
					array (
						'id' => $obj->rowid,
						'entity' => $obj->entity,
						'ref' => $obj->ref,
						'fk_entrepot' => $obj->fk_entrepot,
						'fk_fabrication' => $obj->fk_fabrication,
						'fk_user' => $obj->fk_user,
						'date_creation' => $this->db->jdate($obj->date_creation),
						'date_delivery' => $this->db->jdate($obj->date_delivery),
						'description' => $obj->description,
						'statut' => $obj->statut
					);
					$i++;
				}
				return $this->array;
			}
			$this->db->free($resql);
			$this->fetch_lines();
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_fabrication ".$this->error, LOG_ERR);
			return -1;
		}
	}
	function getlist_op($fk_fabrication)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.ref,";
		$sql.= " t.fk_entrepot_from,";
		$sql.= " t.fk_entrepot,";
		$sql.= " t.fk_fabrication,";
		$sql.= " t.fk_projet,";
		$sql.= " t.fk_user,";
		$sql.= " t.date_creation,";
		$sql.= " t.date_delivery,";
		$sql.= " t.description,";
		$sql.= " t.model_pdf,";
		$sql.= " t.fk_user_create,";
		$sql.= " t.fk_user_mod,";
		$sql.= " t.tms,";
		$sql.= " t.statut";

		$sql.= " FROM ".MAIN_DB_PREFIX."sol_almacen as t";
		$sql.= " WHERE t.fk_fabrication = ".$fk_fabrication;
		$sql.= " AND t.rowid !=".$this->id;

		dol_syslog(get_class($this)."::getlist_op sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->linealm = array();
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$num = $this->db->num_rows($resql);
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$row = new Solalmacenlines($this->db);
					$row->id    = $obj->rowid;
					$row->entity = $obj->entity;
					$row->ref = $obj->ref;
					$row->fk_entrepot_from = $obj->fk_entrepot_from;
					$row->fk_entrepot = $obj->fk_entrepot;
					$row->fk_fabrication = $obj->fk_fabrication;
					$row->fk_projet = $obj->fk_projet;
					$row->fk_user = $obj->fk_user;
					$row->date_creation = $this->db->jdate($obj->date_creation);
					$row->date_delivery = $this->db->jdate($obj->date_delivery);
					$row->description = $obj->description;
					$row->model_pdf = $obj->model_pdf;
					$row->fk_user_create = $obj->fk_user_create;
					$row->fk_user_mod = $obj->fk_user_mod;
					$row->tms = $this->db->jdate($obj->tms);
					$row->statut = $obj->statut;
					$this->linealm[$i] = $row;
					$i++;
				}
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist_op ".$this->error, LOG_ERR);
			return -1;
		}
	}

		/**
	 *  Create a document onto disk according to template model.
	 *
	 *  @param	    string		$modele			Force template to use ('' to not force)
	 *  @param		Translate	$outputlangs	Object lang to use for traduction
	 *  @param      int			$hidedetails    Hide details of lines
	 *  @param      int			$hidedesc       Hide description
	 *  @param      int			$hideref        Hide ref
	 *  @return     int          				0 if KO, 1 if OK
	 */
		public function generateDocument($modele, $outputlangs, $hidedetails=0, $hidedesc=0, $hideref=0)
		{
			global $conf, $user, $langs;

			$langs->load("suppliers");

		// Sets the model on the model name to use
			if (! dol_strlen($modele))
			{
				if (! empty($conf->global->ALMACEN_ADDON_PDF))
				{
					$modele = $conf->global->ALMACEN_ADDON_PDF;
				}
				else
				{
					$modele = 'pedido';
				}
			}

			$modelpath = "almacen/core/modules/doc/";

			return $this->commonGenerateDocument($modelpath, $modele, $outputlangs, $hidedetails, $hidedesc, $hideref);
		}

	/**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatut($mode=0)
	{
		return $this->LibStatut($this->statut,$mode);
	}

	/**
	 *  Renvoi le libelle d'un status donne
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return string 			       	Label of status
	 */
	function LibStatut($statut,$mode=0)
	{
		global $langs;
		if ($mode == 0)
		{
			if ($statut==-2) return $langs->trans('StatusOrderRejected');
			if ($statut==-1) return $langs->trans('StatusOrderCanceled');
			if ($statut==0) return $langs->trans('StatusOrderDraft');
			if ($statut==1) return $langs->trans('StatusOrderValidated');
			if ($statut==6) return $langs->trans('StatusOrderApproved');
			if ($statut==2) return $langs->trans('StatusOrderSent');
			if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderToBill');
			if ($statut==4 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderProcessed');
			if ($statut==5) return $langs->trans('StatusOrderoutofstock');
		}
		elseif ($mode == 1)
		{
			if ($statut==-2) return $langs->trans('StatusOrderRejected');
			if ($statut==-1) return $langs->trans('StatusOrderCanceledShort');
			if ($statut==0) return $langs->trans('StatusOrderDraftShort');
			if ($statut==1) return $langs->trans('StatusOrderValidatedShort');
			if ($statut==6) return $langs->trans('StatusOrderApproved');
			if ($statut==2) return $langs->trans('StatusOrderSentShort');
			if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderToBillShort');
			if ($statut==4 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderProcessed');
			if ($statut==5) return $langs->trans('StatusOrderoutofstock');
		}
		elseif ($mode == 2)
		{
			if ($statut==-2) return img_picto($langs->trans('StatusOrderRejected'),'statut5').' '.$langs->trans('StatusOrderRejectedShort');
			if ($statut==-1) return img_picto($langs->trans('StatusOrderCanceled'),'statut5').' '.$langs->trans('StatusOrderCanceledShort');
			if ($statut==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0').' '.$langs->trans('StatusOrderDraftShort');
			if ($statut==1) return img_picto($langs->trans('StatusOrderValidated'),'statut1').' '.$langs->trans('StatusOrderValidatedShort');
			if ($statut==6) return img_picto($langs->trans('StatusOrderApproved'),'statut6').' '.$langs->trans('StatusOrderApprovedShort');
			if ($statut==2) return img_picto($langs->trans('StatusOrderSent'),'statut3').' '.$langs->trans('StatusOrderSentShort');
			if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7').' '.$langs->trans('StatusOrderToBillShort');
			if ($statut==4 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6').' '.$langs->trans('StatusOrderProcessedShort');
			if ($statut==5) return img_picto($langs->trans('StatusOrderoutofstock'),'statut0').' '.$langs->trans('StatusOrderoutofstock');

		}
		elseif ($mode == 3)
		{
			if ($statut==-2) return img_picto($langs->trans('StatusOrderRejected'),'statut8');
			if ($statut==-1) return img_picto($langs->trans('StatusOrderCanceled'),'statut5');
			if ($statut==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0');
			if ($statut==1) return img_picto($langs->trans('StatusOrderValidated'),'statut1');
			if ($statut==6) return img_picto($langs->trans('StatusOrderApproved'),'statut3');
			if ($statut==2) return img_picto($langs->trans('StatusOrderSentShort'),'statut4');
			if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7');
			if ($statut==4 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6');
			if ($statut==5) return img_picto($langs->trans('StatusOrderoutofstock'),'statut9');
		}
		elseif ($mode == 4)
		{
			if ($statut==-2) return img_picto($langs->trans('StatusOrderRejected'),'statut8').' '.$langs->trans('StatusOrderRejected');
			if ($statut==-1) return img_picto($langs->trans('StatusOrderCanceled'),'statut5').' '.$langs->trans('StatusOrderCanceled');
			if ($statut==0) return img_picto($langs->trans('StatusOrderDraft'),'statut0').' '.$langs->trans('StatusOrderDraft');
			if ($statut==1) return img_picto($langs->trans('StatusOrderValidated'),'statut1').' '.$langs->trans('StatusOrderValidated');
			if ($statut==6) return img_picto($langs->trans('StatusOrderApproved'),'statut3').' '.$langs->trans('StatusOrderApproved');
			if ($statut==2) return img_picto($langs->trans('StatusOrderSentShort'),'statut4').' '.$langs->trans('StatusOrderSent');
			if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7').' '.$langs->trans('StatusOrderToBill');
			if ($statut==4 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed'),'statut6').' '.$langs->trans('StatusOrderProcessed');
			if ($statut==5) return img_picto($langs->trans('StatusOrderoutofstock'),'statut9').' '.$langs->trans('StatusOrderoutofstock');
		}
		elseif ($mode == 5)
		{
			if ($statut==-2) return $langs->trans('StatusOrderRejectedShort').' '.img_picto($langs->trans('StatusOrderRejected'),'statut8');
			if ($statut==-1) return $langs->trans('StatusOrderCanceledShort').' '.img_picto($langs->trans('StatusOrderCanceled'),'statut5');
			if ($statut==0) return $langs->trans('StatusOrderDraftShort').' '.img_picto($langs->trans('StatusOrderDraft'),'statut0');
			if ($statut==1) return $langs->trans('StatusOrderValidatedShort').' '.img_picto($langs->trans('StatusOrderValidated'),'statut1');
			if ($statut==6) return $langs->trans('StatusOrderApprovedShort').' '.img_picto($langs->trans('StatusOrderApproved'),'statut3');
			if ($statut==2) return $langs->trans('StatusOrderSentShort').' '.img_picto($langs->trans('StatusOrderSent'),'statut4');
			if ($statut==3 && (! $facturee && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderToBillShort').' '.img_picto($langs->trans('StatusOrderToBill'),'statut7');
			if ($statut==4 && ($facturee || ! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderProcessedShort').' '.img_picto($langs->trans('StatusOrderProcessed'),'statut6');
			if ($statut==5) return $langs->trans('StatusOrderoutofstock').' '.img_picto($langs->trans('StatusOrderoutofstock'),'statut9');
		}
	}
}

class Solalmacenlines
{
	var $id;
	var $entity;
	var $ref;
	var $fk_entrepot_from;
	var $fk_entrepot;
	var $fk_fabrication;
	var $fk_user;
	var $date_creation='';
	var $date_delivery='';
	var $description;
	var $model_pdf;
	var $fk_user_create;
	var $fk_user_mod;
	var $tms='';
	var $statut;
}
?>