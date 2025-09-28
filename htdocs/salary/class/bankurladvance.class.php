<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *  \file       dev/skeletons/bankurladvance.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2013-12-04 15:00
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Bankurladvance // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='bankurladvance';			//!< Id that identify managed objects
	//var $table_element='bankurladvance';	//!< Name of table without prefix where object is stored

    var $id;
    
	var $fk_bank;
	var $url_id;
	var $url;
	var $label;
	var $type;

    


    /**
     *  Constructor
     *
     *  @param	DoliDb		$db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
        return 1;
    }


    /**
     *  Create object into database
     *
     *  @param	User	$user        User that creates
     *  @param  int		$notrigger   0=launch triggers after, 1=disable triggers
     *  @return int      		   	 <0 if KO, Id of created object if OK
     */
    function create($user, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
        
		if (isset($this->fk_bank)) $this->fk_bank=trim($this->fk_bank);
		if (isset($this->url_id)) $this->url_id=trim($this->url_id);
		if (isset($this->url)) $this->url=trim($this->url);
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->type)) $this->type=trim($this->type);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."bank_url_advance(";
		
		$sql.= "fk_bank,";
		$sql.= "url_id,";
		$sql.= "url,";
		$sql.= "label,";
		$sql.= "type";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->fk_bank)?'NULL':"'".$this->fk_bank."'").",";
		$sql.= " ".(! isset($this->url_id)?'NULL':"'".$this->url_id."'").",";
		$sql.= " ".(! isset($this->url)?'NULL':"'".$this->db->escape($this->url)."'").",";
		$sql.= " ".(! isset($this->label)?'NULL':"'".$this->db->escape($this->label)."'").",";
		$sql.= " ".(! isset($this->type)?'NULL':"'".$this->db->escape($this->type)."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."bank_url_advance");

			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
	            //$interface=new Interfaces($this->db);
	            //$result=$interface->run_triggers('MYOBJECT_CREATE',$this,$user,$langs,$conf);
	            //if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
			}
        }

        // Commit or rollback
        if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::create ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
            return $this->id;
		}
    }


    /**
     *  Load object in memory from the database
     *
     *  @param	int		$id    Id object
     *  @return int          	<0 if KO, >0 if OK
     */
    function fetch($id)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.fk_bank,";
		$sql.= " t.url_id,";
		$sql.= " t.url,";
		$sql.= " t.label,";
		$sql.= " t.type";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."bank_url_advance as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->fk_bank = $obj->fk_bank;
				$this->url_id = $obj->url_id;
				$this->url = $obj->url;
				$this->label = $obj->label;
				$this->type = $obj->type;

                
            }
            $this->db->free($resql);

            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
            return -1;
        }
    }


    /**
     *  Update object into database
     *
     *  @param	User	$user        User that modifies
     *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
     *  @return int     		   	 <0 if KO, >0 if OK
     */
    function update($user=0, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
        
		if (isset($this->fk_bank)) $this->fk_bank=trim($this->fk_bank);
		if (isset($this->url_id)) $this->url_id=trim($this->url_id);
		if (isset($this->url)) $this->url=trim($this->url);
		if (isset($this->label)) $this->label=trim($this->label);
		if (isset($this->type)) $this->type=trim($this->type);

        

		// Check parameters
		// Put here code to add a control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."bank_url_advance SET";
        
		$sql.= " fk_bank=".(isset($this->fk_bank)?$this->fk_bank:"null").",";
		$sql.= " url_id=".(isset($this->url_id)?$this->url_id:"null").",";
		$sql.= " url=".(isset($this->url)?"'".$this->db->escape($this->url)."'":"null").",";
		$sql.= " label=".(isset($this->label)?"'".$this->db->escape($this->label)."'":"null").",";
		$sql.= " type=".(isset($this->type)?"'".$this->db->escape($this->type)."'":"null")."";

        
        $sql.= " WHERE rowid=".$this->id;

		$this->db->begin();

		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action calls a trigger.

	            //// Call triggers
	            //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
	            //$interface=new Interfaces($this->db);
	            //$result=$interface->run_triggers('MYOBJECT_MODIFY',$this,$user,$langs,$conf);
	            //if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
	    	}
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
    }


 	/**
	 *  Delete object in database
	 *
     *	@param  User	$user        User that deletes
     *  @param  int		$notrigger	 0=launch triggers after, 1=disable triggers
	 *  @return	int					 <0 if KO, >0 if OK
	 */
	function delete($user, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;

		$this->db->begin();

		if (! $error)
		{
			if (! $notrigger)
			{
				// Uncomment this and change MYOBJECT to your own tag if you
		        // want this action calls a trigger.

		        //// Call triggers
		        //include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
		        //$interface=new Interfaces($this->db);
		        //$result=$interface->run_triggers('MYOBJECT_DELETE',$this,$user,$langs,$conf);
		        //if ($result < 0) { $error++; $this->errors=$interface->errors; }
		        //// End call triggers
			}
		}

		if (! $error)
		{
    		$sql = "DELETE FROM ".MAIN_DB_PREFIX."bank_url_advance";
    		$sql.= " WHERE rowid=".$this->id;

    		dol_syslog(get_class($this)."::delete sql=".$sql);
    		$resql = $this->db->query($sql);
        	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::delete ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
	}



	/**
	 *	Load an object from its id and create a new one in database
	 *
	 *	@param	int		$fromid     Id of object to clone
	 * 	@return	int					New id of clone
	 */
	function createFromClone($fromid)
	{
		global $user,$langs;

		$error=0;

		$object=new Bankurladvance($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		$object->id=0;
		$object->statut=0;

		// Clear fields
		// ...

		// Create clone
		$result=$object->create($user);

		// Other options
		if ($result < 0)
		{
			$this->error=$object->error;
			$error++;
		}

		if (! $error)
		{


		}

		// End
		if (! $error)
		{
			$this->db->commit();
			return $object->id;
		}
		else
		{
			$this->db->rollback();
			return -1;
		}
	}


	/**
	 *	Initialise object with example values
	 *	Id must be 0 if object instance is a specimen
	 *
	 *	@return	void
	 */
	function initAsSpecimen()
	{
		$this->id=0;
		
		$this->fk_bank='';
		$this->url_id='';
		$this->url='';
		$this->label='';
		$this->type='';

		
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
	function addPaymentToBank($user,$mode,$label,$accountid,$bank_line_id,$emetteur_nom,$emetteur_banque,$notrigger=0)
    {
        global $conf,$langs,$user;

        $error=0;
        //$bank_line_id=0;

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
            if (empty($totalamount)) $totalamount=$this->total; // For backward compatibility
            if ($mode == 'advance') $totalamount=$totalamount;

            // // // Insert payment into llx_bank
            // // $bank_line_id = $acc->addline(
            // //     $this->datepaye,
            // //     $this->paiementid,  // Payment mode id or code ("CHQ or VIR for example")
            // //     $label,
            // //     $totalamount,		// Sign must be positive when we receive money (customer payment), negative when you give money (supplier invoice or credit note)
            // //     $this->num_paiement,
            // //     '',
            // //     $user,
            // //     $emetteur_nom,
            // //     $emetteur_banque
            // // );

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
                    if ($mode == 'advance') $url=DOL_URL_ROOT.'/compta/paiement/fiche.php?id=';
                    // if ($mode == 'payment_supplier') $url=DOL_URL_ROOT.'/fourn/paiement/fiche.php?id=';
                    if ($url)
                    {
                        $result=$acc->add_url_line($bank_line_id, $this->id, $url, '(advance)', $mode);
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
                    foreach ($this->amounts as $key => $value)  // We should have always same third party but we loop in case of.
                    {
                        if ($mode == 'payment')
                        {
                            $fac = new Facture($this->db);
                            $fac->fetch($key);
                            $fac->fetch_thirdparty();
                            if (! in_array($fac->thirdparty->id,$linkaddedforthirdparty)) // Not yet done for this thirdparty
                            {
                                $result=$acc->add_url_line(
                                    $bank_line_id,
                                    $fac->thirdparty->id,
                                    DOL_URL_ROOT.'/comm/fiche.php?socid=',
                                    $fac->thirdparty->nom,
                                    'company'
                                );
                                if ($result <= 0) dol_print_error($this->db);
                                $linkaddedforthirdparty[$fac->thirdparty->id]=$fac->thirdparty->id;  // Mark as done for this thirdparty
                            }
                        }
                        if ($mode == 'payment_supplier')
                        {
                            $fac = new FactureFournisseur($this->db);
                            $fac->fetch($key);
                            $fac->fetch_thirdparty();
                            if (! in_array($fac->thirdparty->id,$linkaddedforthirdparty)) // Not yet done for this thirdparty
                            {
                                $result=$acc->add_url_line(
                                    $bank_line_id,
                                    $fac->thirdparty->id,
                                    DOL_URL_ROOT.'/fourn/fiche.php?socid=',
                                    $fac->thirdparty->nom,
                                    'company'
                                );
                                if ($result <= 0) dol_print_error($this->db);
                                $linkaddedforthirdparty[$fac->thirdparty->id]=$fac->thirdparty->id;  // Mark as done for this thirdparty
                            }
                        }
                    }
                }

	            if (! $error && ! $notrigger)
				{
					// Appel des triggers
					include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
					$interface=new Interfaces($this->db);
					$result=$interface->run_triggers('PAYMENT_ADD_TO_BANK',$this,$user,$langs,$conf);
					if ($result < 0) { $error++; $this->errors=$interface->errors; }
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
     *      Add a link between bank line record and its source
     *
     *      @param	int		$line_id    Id ecriture bancaire
     *      @param  int		$url_id     Id parametre url
     *      @param  string	$url        Url
     *      @param  string	$label      Link label
     *      @param  string	$type       Type of link ('payment', 'company', 'member', ...)
     *      @return int         		<0 if KO, id line if OK
     */
    function add_url_line($line_id, $url_id, $url, $label, $type)
    {
        $sql = "INSERT INTO ".MAIN_DB_PREFIX."bank_url (";
        $sql.= "fk_bank";
        $sql.= ", url_id";
        $sql.= ", url";
        $sql.= ", label";
        $sql.= ", type";
        $sql.= ") VALUES (";
        $sql.= "'".$line_id."'";
        $sql.= ", '".$url_id."'";
        $sql.= ", '".$url."'";
        $sql.= ", '".$this->db->escape($label)."'";
        $sql.= ", '".$type."'";
        $sql.= ")";

        dol_syslog(get_class($this)."::add_url_line sql=".$sql);
        if ($this->db->query($sql))
        {
            $rowid = $this->db->last_insert_id(MAIN_DB_PREFIX."bank_url");
            return $rowid;
        }
        else
        {
            $this->error=$this->db->lasterror();
            dol_syslog(get_class($this)."::add_url_line ".$this->error, LOG_ERR);
            return -1;
        }
    }

    function createpaiement($datepaye,$totalamount,$paiementid,$num_paiement,$note)
    {
      global $conf,$user;
      //$this->db->begin();
      $now=dol_now();
      $id = 0;
      $sql = "INSERT INTO ".MAIN_DB_PREFIX."paiement (entity, datec, datep, amount, fk_paiement, num_paiement, note, fk_user_creat)";
      $sql.= " VALUES (".$conf->entity.", '".$this->db->idate($now)."', '".$this->db->idate($datepaye)."', '".$totalamount."', ".$paiementid.", '".$num_paiement."', '".$this->db->escape($note)."', ".$user->id.")";
      
      dol_syslog(get_class($this)."::Create insert paiement sql=".$sql);
      $resql = $this->db->query($sql);
      if ($resql)
	{
	  $id = $this->db->last_insert_id(MAIN_DB_PREFIX.'paiement');
	  //$this->db->commit();

	}
      else
	return -1;
      return $id;
    }
}
?>
