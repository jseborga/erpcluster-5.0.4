<?php
require_once DOL_DOCUMENT_ROOT.'/advancepayment/class/paiementfournadvance.class.php';

class Paiementfournadvanceext extends Paiementfournadvance
{

	/**
	 *  Create a document onto disk according to template model.
	 *
	 *  @param      string      $modele         Force template to use ('' to not force)
	 *  @param      Translate   $outputlangs    Object lang to use for traduction
	 *  @param      int         $hidedetails    Hide details of lines
	 *  @param      int         $hidedesc       Hide description
	 *  @param      int         $hideref        Hide ref
	 *  @return     int                         0 if KO, 1 if OK
	 */
	public function generateDocument($modele, $outputlangs, $hidedetails=0, $hidedesc=0, $hideref=0)
	{
		global $conf, $user, $langs;

		$langs->load("suppliers");

		// Sets the model on the model name to use
		if (! dol_strlen($modele))
		{
			if (! empty($conf->global->ADVANCEPAYMENT_ADDON_PDF))
			{
				$modele = $conf->global->ADVANCEPAYMENT_ADDON_PDF;
			}
			else
			{
				$modele = 'paymentfournbo';
			}
		}

		$modelpath = "advancepayment/core/modules/doc/";

		return $this->commonGenerateDocument($modelpath, $modele, $outputlangs, $hidedetails, $hidedesc, $hideref);
	}

	/**
	 *  Returns the reference to the following non used Order depending on the active numbering module
	 *  defined into ALMACEN_ADDON
	 *
	 *  @param  Societe     $soc    Object thirdparty
	 *  @return string              Order free reference
	 */
	function getNextNumRef($soc,$mode='next')
	{
		global $db, $langs, $conf;
		$langs->load("advancepayment@advancepayment");

		$dir = DOL_DOCUMENT_ROOT . "/advancepayment/core/modules";

		if (! empty($conf->global->ADVANCEPAYMENT_ADDON))
		{
			$file = $conf->global->ADVANCEPAYMENT_ADDON.".php";
			// Chargement de la classe de numerotation
			$classname = $conf->global->ADVANCEPAYMENT_ADDON;
			//cambiamos a uno fijo
			//$file = 'mod_almacen_ubuntubo.php';
			//$classname = 'mod_almacen_ubuntubo';
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
					dol_print_error($db,"Paiementfournadvanceext::getNextNumRef ".$obj->error);
					return "";
				}
			}
			else
			{
				print $langs->trans("Error")." ".$langs->trans("Error_ADVANCEPAIEMENT_ADDON_NotDefined");
				return "";
			}
		}
		else
		{
			print $langs->trans("Error")." ".$langs->trans("Error_ADVANCEPAIEMENT_ADDON_NotDefined");
			return "";
		}
	}

	/**
	 *      Add a record into bank for payment with links between this bank record and invoices of payment.
	 *      All payment properties (this->amount, this->amounts, ...) must have been set first like after a call to create().
	 *
	 *      @param	User	$user               Object of user making payment
	 *      @param  string	$mode               'payment', 'payment_supplier'
	 *      @param  string	$label              Label to use in bank record
	 *      @param  int		$accountid          Id of bank account to do link with
	 *      @param  string	$emetteur_nom       Name of transmitter
	 *      @param  string	$emetteur_banque    Name of bank
	 *      @param	int		$notrigger			No trigger
	 *      @return int                 		<0 if KO, bank_line_id if OK
	 */

	function addPaymentToBankadd($user,$mode,$label,$accountid,$emetteur_nom,$emetteur_banque,$notrigger=0)
	{
		global $conf,$langs,$user;

		$error=0;
		$bank_line_id=0;

		if (! empty($conf->banque->enabled))
		{
			if ($accountid <= 0)
			{
				$this->error='Bad value for parameter accountid';
				dol_syslog(get_class($this).'::addPaymentToBank '.$this->error, LOG_ERR);
				return -1;
			}

			$this->db->begin();

			$this->fk_account=$accountid;

			require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

			dol_syslog("$user->id,$mode,$label,$this->fk_account,$emetteur_nom,$emetteur_banque");

			$acc = new Account($this->db);
			$result=$acc->fetch($this->fk_account);
			
			$totalamount=$this->amount;
			if (empty($totalamount)) $totalamount=$this->total; 
			// For backward compatibility
			
			// if dolibarr currency != bank currency then we received an amount in customer currency (currently I don't manage the case : my currency is USD, the customer currency is EUR and he paid me in GBP. Seems no sense for me)
			if (!empty($conf->multicurrency->enabled) && $conf->currency != $acc->currency_code) $totalamount=$this->multicurrency_amount;

			if ($mode == 'payment_supplier' || $mode == 'payment_supplier_advance') $totalamount=-$totalamount;

			// Insert payment into llx_bank
			$bank_line_id = $acc->addline(
				$this->datepaye,
				$this->paiementid,  
				// Payment mode id or code ("CHQ or VIR for example")
				$label,
				$totalamount,		
				// Sign must be positive when we receive money (customer payment), negative when you give money (supplier invoice or credit note)
				$this->num_paiement,
				'',
				$user,
				$emetteur_nom,
				$emetteur_banque
				);

			// Mise a jour fk_bank dans llx_paiement
			// On connait ainsi le paiement qui a genere l'ecriture bancaire
			if ($bank_line_id > 0)
			{
				$result=$this->update_fk_bank($bank_line_id);
				if ($result <= 0)
				{
					$error++;
					dol_print_error($this->db);
				}

				// Add link 'payment', 'payment_supplier' in bank_url between payment and bank transaction
				if ( ! $error)
				{
					$url='';
					if ($mode == 'payment') $url=DOL_URL_ROOT.'/compta/paiement/card.php?id=';
					if ($mode == 'payment_supplier') $url=DOL_URL_ROOT.'/fourn/paiement/card.php?id=';
					if ($mode == 'payment_supplier_advance') $url=DOL_URL_ROOT.'/advancepayment/fourn/card.php?id=';
					if ($url)
					{
						$result=$acc->add_url_line($bank_line_id, $this->id, $url, '(paiement)', $mode);
						if ($result <= 0)
						{
							$error++;
							dol_print_error($this->db);
						}
					}
				}

				// Add link 'company' in bank_url between invoice and bank transaction (for each invoice concerned by payment)
				if (! $error  && $label != '(WithdrawalPayment)')
				{
					$linkaddedforthirdparty=array();

					foreach ($this->amounts as $key => $value)  
					// We should have always same third party but we loop in case of.
					{
						if ($mode == 'payment')
						{
							$fac = new Facture($this->db);
							$fac->fetch($key);
							$fac->fetch_thirdparty();
							if (! in_array($fac->thirdparty->id,$linkaddedforthirdparty)) 
							// Not yet done for this thirdparty
							{
								$result=$acc->add_url_line(
									$bank_line_id,
									$fac->thirdparty->id,
									DOL_URL_ROOT.'/comm/card.php?socid=',
									$fac->thirdparty->name,
									'company'
									);
								if ($result <= 0) dol_syslog(get_class($this).'::addPaymentToBankadd '.$this->db->lasterror());
								$linkaddedforthirdparty[$fac->thirdparty->id]=$fac->thirdparty->id;  
								// Mark as done for this thirdparty
							}
						}
						if ($mode == 'payment_supplier')
						{
							$fac = new FactureFournisseur($this->db);
							$fac->fetch($key);
							$fac->fetch_thirdparty();
							if (! in_array($fac->thirdparty->id,$linkaddedforthirdparty)) 
							// Not yet done for this thirdparty
							{
								$result=$acc->add_url_line(
									$bank_line_id,
									$fac->thirdparty->id,
									DOL_URL_ROOT.'/fourn/card.php?socid=',
									$fac->thirdparty->name,
									'company'
									);
								if ($result <= 0) dol_syslog(get_class($this).'::addPaymentToBankadd '.$this->db->lasterror());
								$linkaddedforthirdparty[$fac->thirdparty->id]=$fac->thirdparty->id;  
								// Mark as done for this thirdparty
							}
						}
						if ($mode == 'payment_supplier_advance')
						{
							require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
							$fac = new CommandeFournisseur($this->db);

							$fac->fetch($key);
							//agregamos variables
							$socid = $fac->socid;
							$societe = new Societe($this->db);
							$societe->fetch($fac->socid);
							$name = $societe->name;
							//$fac->fetch_thirdparty();
							//if (! in_array($fac->thirdparty->id,$linkaddedforthirdparty))
							// Not yet done for this thirdparty
							//{
							$result=$acc->add_url_line(
								$bank_line_id,
								$socid,
								DOL_URL_ROOT.'/fourn/commande/card.php?socid=',
								$name,
								'company'
								);
							if ($result <= 0) dol_syslog(get_class($this).'::addPaymentToBankadd '.$this->db->lasterror());
							$linkaddedforthirdparty[$fac->thirdparty->id]=$fac->thirdparty->id;  
								// Mark as done for this thirdparty
							//}
						}
					}
				}

				// Add link 'WithdrawalPayment' in bank_url
				if (! $error && $label == '(WithdrawalPayment)')
				{
					$result=$acc->add_url_line(
						$bank_line_id,
						$this->id_prelevement,
						DOL_URL_ROOT.'/compta/prelevement/card.php?id=',
						$this->num_paiement,
						'withdraw'
						);
				}

				if (! $error && ! $notrigger)
				{
					// Appel des triggers
					$result=$this->call_trigger('PAYMENT_ADD_TO_BANK', $user);
					if ($result < 0) { $error++; }
					// Fin appel triggers
				}
			}
			else
			{
				$this->error=$acc->error;
				$error++;
			}

			if (! $error)
			{
				$this->db->commit();
			}
			else
			{
				$this->db->rollback();
			}
		}

		if (! $error)
		{
			return $bank_line_id;
		}
		else
		{
			return -1;
		}
	}

	/**
	 *      Mise a jour du lien entre le paiement et la ligne generee dans llx_bank
	 *
	 *      @param  int     $id_bank    Id compte bancaire
	 *      @return int                 <0 if KO, >0 if OK
	 */
	function update_fk_bank($id_bank)
	{
		$sql = 'UPDATE '.MAIN_DB_PREFIX.$this->table_element.' set fk_bank = '.$id_bank;
		$sql.= ' WHERE rowid = '.$this->id;

		dol_syslog(get_class($this).'::update_fk_bank', LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result)
		{
			return 1;
		}
		else
		{
			$this->error=$this->db->lasterror();
			dol_syslog(get_class($this).'::update_fk_bank '.$this->error);
			return -1;
		}
	}
}
?>