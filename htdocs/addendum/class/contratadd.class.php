<?php
include_once(DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php');
class ContratAdd extends Contrat
{
  	// public $element='contrat';
	// public $table_element='contrat';
	// public $table_element_line='contratdet';
	// public $fk_element='fk_contrat';
	// protected $ismultientitymanaged = 1;	// 0=No test on entity, 1=Test with field entity, 2=Test with link by societe

	// /**
	//  * {@inheritdoc}
	//  */
	// protected $table_ref_field = 'ref';

	// /**
	//  * Id of the contract
	//  * @var int
	//  */
	// var $id;

	// /**
	//  * Reference of the contract
	//  * @var string
	//  */
	// var $ref;

	// /**
	//  * External reference of the contract.
	//  * Used by 3rd party services
	//  * @var string
	//  */
	// var $ref_ext;

	// /**
	//  * Supplier reference of the contract
	//  * @var string
	//  */
	// var $ref_supplier;

	// /**
	//  * Client id linked to the contract
	//  * @var int
	//  */
	// var $socid;
	// var $societe;		// Objet societe

	// /**
	//  * Status of the contract
	//  * @var int
	//  */
	// var $statut=0;		// 0=Draft,
	// var $product;

	// /**
	//  * @var int		Id of user author of the contract
	//  */
	// public $fk_user_author;

	// /**
	//  * TODO: Which is the correct one?
	//  * Author of the contract
	//  * @var int
	//  */
	// public $user_author_id;

	// /**
	//  * @var User 	Object user that create the contract. Set by the info method.
	//  */
	// public $user_creation;

	// /**
	//  * @var User 	Object user that close the contract. Set by the info method.
	//  */
	// public $user_cloture;

	// /**
	//  * @var int		Date of creation
	//  */
	// var $date_creation;

	// /**
	//  * @var int		Date of last modification. Not filled until you call ->info()
	//  */
	// public $date_modification;

	// /**
	//  * @var int		Date of validation
	//  */
	// var $date_validation;

	// /**
	//  * @var int		Date when contract was signed
	//  */
	// var $date_contrat;

	// /**
	//  * @var int		Date of contract closure
	//  * @deprecated we close contract lines, not a contract
	//  */
	// var $date_cloture;

	// var $commercial_signature_id;
	// var $commercial_suivi_id;

	// /**
	//  * @var string	Private note
	//  */
	// var $note_private;

	// /**
	//  * @var string	Public note
	//  */
	// var $note_public;

	// var $modelpdf;

	// /**
	//  * @deprecated Use fk_project instead
	//  * @see fk_project
	//  */
	// var $fk_projet;

	// public $fk_project;

	// var $extraparams=array();

	// /**
	//  * @var ContratLigne[]		Contract lines
	//  */
	// var $lines=array();


	/**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * Validate a contract
	 *
	 * @param	User	$user      		Objet User
	 * @param   string	$force_number	Reference to force on contract (not implemented yet)
     * @param	int		$notrigger		1=Does not execute triggers, 0= execuete triggers
	 * @return	int						<0 if KO, >0 if OK
	 */
	function validate($user, $force_number='', $notrigger=0)
	{
		require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
		global $langs, $conf,$thirdparty;

		$now=dol_now();

		$error=0;
		dol_syslog(get_class($this).'::validate user='.$user->id.', force_number='.$force_number);


		$this->db->begin();

		$this->fetch_thirdparty();
		
		// A contract is validated so we can move thirdparty to status customer
		$result=$this->thirdparty->set_as_client();
		
		// Define new ref
		if (! $error && (preg_match('/^[\(]?PROV/i', $this->ref) || empty($this->ref))) // empty should not happened, but when it occurs, the test save life
		{
			$num = $this->getNextNumRef($this->thirdparty);
		}
		else
		{
			$num = $this->ref;
		}
        $this->newref = $num;

		if ($num)
		{
			$sql = "UPDATE ".MAIN_DB_PREFIX."contrat SET ref = '".$num."', statut = 1";
			//$sql.= ", fk_user_valid = ".$user->id.", date_valid = '".$this->db->idate($now)."'";
			$sql .= " WHERE rowid = ".$this->id . " AND statut = 0";

			dol_syslog(get_class($this)."::validate", LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (! $resql)
			{
				dol_print_error($this->db);
				$error++;
				$this->error=$this->db->lasterror();
			}

			// Trigger calls
			if (! $error && ! $notrigger)
			{
                // Call trigger
                $result=$this->call_trigger('CONTRACT_VALIDATE',$user);
                if ($result < 0) { $error++; }
                // End call triggers
			}

			if (! $error)
			{
            	$this->oldref = $this->ref;

				// Rename directory if dir was a temporary ref
				if (preg_match('/^[\(]?PROV/i', $this->ref))
				{
					// Rename of object directory ($this->ref = old ref, $num = new ref)
					// to  not lose the linked files
					$oldref = dol_sanitizeFileName($this->ref);
					$newref = dol_sanitizeFileName($num);
					$dirsource = $conf->contract->dir_output.'/'.$oldref;
					$dirdest = $conf->contract->dir_output.'/'.$newref;
					if (file_exists($dirsource))
					{
						dol_syslog(get_class($this)."::validate rename dir ".$dirsource." into ".$dirdest);

						if (@rename($dirsource, $dirdest))
						{
							dol_syslog("Rename ok");
						    // Rename docs starting with $oldref with $newref
            				$listoffiles=dol_dir_list($conf->contract->dir_output.'/'.$newref, 'files', 1, '^'.preg_quote($oldref,'/'));
            				foreach($listoffiles as $fileentry)
            				{
            					$dirsource=$fileentry['name'];
            					$dirdest=preg_replace('/^'.preg_quote($oldref,'/').'/',$newref, $dirsource);
            					$dirsource=$fileentry['path'].'/'.$dirsource;
            					$dirdest=$fileentry['path'].'/'.$dirdest;
            					@rename($dirsource, $dirdest);
            				}
						}
					}
				}
			}

			// Set new ref and define current statut
			if (! $error)
			{
				$this->ref = $num;
				$this->statut=1;
				$this->brouillon=0;
				$this->date_validation=$now;
			}
		}
		else
		{
			$error++;
		}

		if (! $error)
		{
			$this->db->commit();
			return 1;
		}
		else
		{
			$this->db->rollback();
			return -1;
		}

	}

}
?>