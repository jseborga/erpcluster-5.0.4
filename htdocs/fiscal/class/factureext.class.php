<?php
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';

class Factureext extends Facture
{

	/**
	 *	Delete invoice
	 *
	 *	@param     	User	$user      	    User making the deletion.
	 *	@param		int		$notrigger		1=Does not execute triggers, 0= execute triggers
	 *	@param		int		$idwarehouse	Id warehouse to use for stock change.
	 *	@return		int						<0 if KO, >0 if OK
	 */
	function deleteadd($user, $notrigger=0, $idwarehouse=-1,$coste=false)
	{
		global $langs,$conf;
		require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

		if (empty($rowid)) $rowid=$this->id;

		dol_syslog(get_class($this)."::delete rowid=".$rowid, LOG_DEBUG);

		// TODO Test if there is at least one payment. If yes, refuse to delete.

		$error=0;
		$this->db->begin();

		if (! $error && ! $notrigger)
		{
            // Call trigger
			$result=$this->call_trigger('BILL_DELETE',$user);
			if ($result < 0) $error++;
            // End call triggers
		}

		// Removed extrafields
		if (! $error) {
			$result=$this->deleteExtraFields();
			if ($result < 0)
			{
				$error++;
				dol_syslog(get_class($this)."::delete error deleteExtraFields ".$this->error, LOG_ERR);
			}
		}

		if (! $error)
		{
			// Delete linked object
			$res = $this->deleteObjectLinked();
			if ($res < 0) $error++;
		}

		if (! $error)
		{
			// If invoice was converted into a discount not yet consumed, we remove discount
			$sql = 'DELETE FROM '.MAIN_DB_PREFIX.'societe_remise_except';
			$sql.= ' WHERE fk_facture_source = '.$rowid;
			$sql.= ' AND fk_facture_line IS NULL';
			$resql=$this->db->query($sql);

			// If invoice has consumned discounts
			$this->fetch_lines();
			$list_rowid_det=array();
			foreach($this->lines as $key => $invoiceline)
			{
				$list_rowid_det[]=$invoiceline->rowid;
			}

			// Consumned discounts are freed
			if (count($list_rowid_det))
			{
				$sql = 'UPDATE '.MAIN_DB_PREFIX.'societe_remise_except';
				$sql.= ' SET fk_facture = NULL, fk_facture_line = NULL';
				$sql.= ' WHERE fk_facture_line IN ('.join(',',$list_rowid_det).')';

				dol_syslog(get_class($this)."::delete", LOG_DEBUG);
				if (! $this->db->query($sql))
				{
					$this->error=$this->db->error()." sql=".$sql;
					$this->db->rollback();
					return -5;
				}
			}

			// If we decrement stock on invoice validation, we increment
			if ($this->type != self::TYPE_DEPOSIT && $result >= 0 && ! empty($conf->stock->enabled) && ! empty($conf->global->STOCK_CALCULATE_ON_BILL) && $idwarehouse!=-1)
				{
					require_once DOL_DOCUMENT_ROOT.'/product/stock/class/mouvementstock.class.php';
					require_once DOL_DOCUMENT_ROOT.'/almacen/class/stockmouvementaddext.class.php';
					$langs->load("agenda");

					$num=count($this->lines);
					for ($i = 0; $i < $num; $i++)
					{
						if ($this->lines[$i]->fk_product > 0)
						{
							$mouvP = new MouvementStock($this->db);
							$mouvPtmp = new MouvementStock($this->db);
							$mouvP->origin = &$this;
							$mouvPadd = new Stockmouvementaddext($this->db);
							$price = 0;
							if ($coste){
							//vamos a buscar el precio al cual salio el producto
								$filter = " AND t.fk_facturedet = ".$this->lines[$i]->id;
								$resadd = $mouvPadd->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
								if ($resadd==1)
								{
									if ($conf->global->ALMACEN_METHOD_VALUATION_INVENTORY==0)
									{
									//VALUACION PPP
										$mouvPtmp->fetch($mouvPadd->fk_stock_mouvement);
										$price = $mouvPtmp->price;
									}
									elseif($conf->global->ALMACEN_METHOD_VALUATION_INVENTORY==1)
									{
									//VALUATION PEPS
										$price = $mouvPadd->value_peps;
									}
								}
							}
						// We decrease stock for product
							if ($this->type == self::TYPE_CREDIT_NOTE) $result=$mouvP->livraison($user, $this->lines[$i]->fk_product, $idwarehouse, $this->lines[$i]->qty, $this->lines[$i]->subprice, $langs->trans("InvoiceDeleteDolibarr",$this->ref));
						else $result=$mouvP->reception($user, $this->lines[$i]->fk_product, $idwarehouse, $this->lines[$i]->qty, $price, $langs->trans("InvoiceDeleteDolibarr",$this->ref));	// we use 0 for price, to not change the weighted average value
					}
				}
			}
			//eliminamos si tiene las lineas de facturedet_fiscal
			$sql = " DELETE FROM ".MAIN_DB_PREFIX."facturedet_fiscal ";
			$sql.= " WHERE fk_facturedet IN (".join(',',$list_rowid_det).")";
			$resql=$this->db->query($sql);
			if ($resql)
			{
			}
			else
			{
				$this->error=$this->db->lasterror()." sql=".$sql;
				$this->db->rollback();
				return -7;
			}

			// Delete invoice line
			$sql = 'DELETE FROM '.MAIN_DB_PREFIX.'facturedet WHERE fk_facture = '.$rowid;

			dol_syslog(get_class($this)."::delete", LOG_DEBUG);

			if ($this->db->query($sql) && $this->delete_linked_contact())
			{
				$sql = 'DELETE FROM '.MAIN_DB_PREFIX.'facture WHERE rowid = '.$rowid;

				dol_syslog(get_class($this)."::delete", LOG_DEBUG);

				$resql=$this->db->query($sql);
				if ($resql)
				{
					// On efface le repertoire de pdf provisoire
					$ref = dol_sanitizeFileName($this->ref);
					if ($conf->facture->dir_output && !empty($this->ref))
					{
						$dir = $conf->facture->dir_output . "/" . $ref;
						$file = $conf->facture->dir_output . "/" . $ref . "/" . $ref . ".pdf";
						if (file_exists($file))	// We must delete all files before deleting directory
						{
							$ret=dol_delete_preview($this);

							if (! dol_delete_file($file,0,0,0,$this)) // For triggers
							{
								$this->error=$langs->trans("ErrorCanNotDeleteFile",$file);
								$this->db->rollback();
								return 0;
							}
						}
						if (file_exists($dir))
						{
							if (! dol_delete_dir_recursive($dir)) // For remove dir and meta
							{
								$this->error=$langs->trans("ErrorCanNotDeleteDir",$dir);
								$this->db->rollback();
								return 0;
							}
						}
					}
					$this->db->commit();
					return 1;
				}
				else
				{
					$this->error=$this->db->lasterror()." sql=".$sql;
					$this->db->rollback();
					return -6;
				}
			}
			else
			{
				$this->error=$this->db->lasterror()." sql=".$sql;
				$this->db->rollback();
				return -4;
			}
		}
		else
		{
			$this->db->rollback();
			return -2;
		}
	}
}
?>