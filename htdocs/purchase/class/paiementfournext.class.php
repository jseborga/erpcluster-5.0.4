<?php
require_once DOL_DOCUMENT_ROOT.'/fourn/class/paiementfourn.class.php';

class PaiementFournext extends PaiementFourn
{
		/**
	 *  Create payment in database
	 *
	 *  @param      User    $user                   Object of creating user
	 *  @param      int     $closepaidinvoices      1=Also close payed invoices to paid, 0=Do nothing more
	 *  @return     int                             id of created payment, < 0 if error
	 */
		function createadd($user, $closepaidinvoices=0)
		{
			global $langs,$conf;

			$error = 0;
			$way = $this->getWay();

		// Clean parameters
			$totalamount = 0;
			$totalamount_converted = 0;

			dol_syslog(get_class($this)."::create", LOG_DEBUG);

			if ($way == 'dolibarr')
			{
				$amounts = &$this->amounts;
				$amounts_to_update = &$this->multicurrency_amounts;
			}
			else
			{
				$amounts = &$this->multicurrency_amounts;
				$amounts_to_update = &$this->amounts;
			}

			foreach ($amounts as $key => $value)
			{
				$value_converted = Multicurrency::getAmountConversionFromInvoiceRate($key, $value, $way, 'facture_fourn');
				$totalamount_converted += $value_converted;
				$amounts_to_update[$key] = price2num($value_converted, 'MT');

				$newvalue = price2num($value,'MT');
				$amounts[$key] = $newvalue;
				$totalamount += $newvalue;
			}
			$totalamount = price2num($totalamount);
			$totalamount_converted = price2num($totalamount_converted);

			$this->db->begin();

		if ($totalamount <> 0) // On accepte les montants negatifs
		{
			$ref = $this->getNextNumRef('');
			$now=dol_now();

			if ($way == 'dolibarr')
			{
				$total = $totalamount;
				$mtotal = $totalamount_converted; // Maybe use price2num with MT for the converted value
			}
			else
			{
				$total = $totalamount_converted; // Maybe use price2num with MT for the converted value
				$mtotal = $totalamount;
			}

			$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'paiementfourn (';
			$sql.= 'ref, entity, datec, datep, amount, multicurrency_amount, fk_paiement, num_paiement, note, fk_user_author, fk_bank)';
			$sql.= " VALUES ('".$this->db->escape($ref)."', ".$conf->entity.", '".$this->db->idate($now)."',";
			$sql.= " '".$this->db->idate($this->datepaye)."', '".$total."', '".$mtotal."', ".$this->paiementid.", '".$this->num_paiement."', '".$this->db->escape($this->note)."', ".$user->id.", 0)";

			$resql = $this->db->query($sql);
			if ($resql)
			{
				$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX.'paiementfourn');

				// Insere tableau des montants / factures
				foreach ($this->amounts as $key => $amount)
				{
					$facid = $key;
					if (is_numeric($amount) && $amount <> 0)
					{
						$amount = price2num($amount);
						$sql = 'INSERT INTO '.MAIN_DB_PREFIX.'paiementfourn_facturefourn (fk_facturefourn, fk_paiementfourn, amount, multicurrency_amount)';
						$sql .= ' VALUES ('.$facid.','. $this->id.',\''.$amount.'\', \''.$this->multicurrency_amounts[$key].'\')';
						$resql=$this->db->query($sql);
						if ($resql)
						{
							// If we want to closed payed invoices
							if ($closepaidinvoices)
							{
								require_once DOL_DOCUMENT_ROOT.'/purchase/class/facturefournadd.class.php';
								require_once DOL_DOCUMENT_ROOT.'/fiscal/class/ctypefacture.class.php';
								$invoice=new FactureFournisseur($this->db);
								$invoice->fetch($facid);
								$retention = 0;
								$objectadd = new Facturefournadd($this->db);
								$objCtypefacture = new Ctypefacture($this->db);
								$resadd = $objectadd->fetch(0,$facid);
								if ($resadd==1)
								{
									$restype = $objCtypefacture->fetch(0,$objectadd->code_facture);
									if ($restype==1)
										$retention = $objCtypefacture->retention;
								}
								$paiement = $invoice->getSommePaiement();
								//$creditnotes=$invoice->getSumCreditNotesUsed();
								$creditnotes=0;
								//$deposits=$invoice->getSumDepositsUsed();
								$deposits=0;
								$totalttc = $invoice->total_ttc;
								if ($retention)
								{
									$totalttc = $invoice->total_ttc-$invoice->total_localtax1-$invoice->total_localtax2;
								}
								$alreadypayed=price2num($paiement + $creditnotes + $deposits,'MT');
								//$remaintopay=price2num($invoice->total_ttc - $paiement - $creditnotes - $deposits,'MT');
								$remaintopay=price2num($totalttc - $paiement - $creditnotes - $deposits,'MT');
								if ($remaintopay == 0)
								{
									$result=$invoice->set_paid($user, '', '');
								}
								else dol_syslog("Remain to pay for invoice ".$facid." not null. We do nothing.");
							}
						}
						else
						{
							dol_syslog('Paiement::Create Erreur INSERT dans paiement_facture '.$facid);
							$error++;
						}

					}
					else
					{
						dol_syslog('PaiementFourn::Create Montant non numerique',LOG_ERR);
					}
				}

				if (! $error)
				{
					// Call trigger
					$result=$this->call_trigger('PAYMENT_SUPPLIER_CREATE',$user);
					if ($result < 0) $error++;
					// End call triggers
				}
			}
			else
			{
				$this->error=$this->db->lasterror();
				$error++;
			}
		}
		else
		{
			$this->error="ErrorTotalIsNull";
			dol_syslog('PaiementFourn::Create Error '.$this->error, LOG_ERR);
			$error++;
		}

		if ($totalamount <> 0 && $error == 0) // On accepte les montants negatifs
		{
			$this->amount=$total;
			$this->total=$total;
			$this->multicurrency_amount=$mtotal;
			$this->db->commit();
			dol_syslog('PaiementFourn::Create Ok Total = '.$this->total);
			return $this->id;
		}
		else
		{
			$this->db->rollback();
			return -1;
		}
	}
}
?>