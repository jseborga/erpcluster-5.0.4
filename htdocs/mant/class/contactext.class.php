<?php
require_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';

class Contactext extends Contact
{
	var $lines;

	/**
	 *  Load object contact
	 *
	 *  @param      int		$id          id du contact
	 *  @param      User	$user        Utilisateur (abonnes aux alertes) qui veut les alertes de ce contact
     *  @param      string  $ref_ext     External reference, not given by Dolibarr
	 *  @return     int     		     -1 if KO, 0 if OK but not found, 1 if OK
	 */
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	{
		global $langs;

		$langs->load("companies");

		$sql = "SELECT c.rowid, c.fk_soc, c.ref_ext, c.civility as civility_id, c.lastname, c.firstname,";
		$sql.= " c.address, c.statut, c.zip, c.town,";
		$sql.= " c.fk_pays as country_id,";
		$sql.= " c.fk_departement,";
		$sql.= " c.birthday,";
		$sql.= " c.poste, c.phone, c.phone_perso, c.phone_mobile, c.fax, c.email, c.jabberid, c.skype,";
        $sql.= " c.photo,";
		$sql.= " c.priv, c.note_private, c.note_public, c.default_lang, c.no_email, c.canvas,";
		$sql.= " c.import_key,";
		$sql.= " co.label as country, co.code as country_code,";
		$sql.= " d.nom as state, d.code_departement as state_code,";
		$sql.= " u.rowid as user_id, u.login as user_login,";
		$sql.= " s.nom as socname, s.address as socaddress, s.zip as soccp, s.town as soccity, s.default_lang as socdefault_lang";
		$sql.= " FROM ".MAIN_DB_PREFIX."socpeople as c";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_country as co ON c.fk_pays = co.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_departements as d ON c.fk_departement = d.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."user as u ON c.rowid = u.fk_socpeople";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."societe as s ON c.fk_soc = s.rowid";
		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		$sql.= ' WHERE 1 = 1';
		if (! empty($conf->multicompany->enabled)) {
		    $sql .= " AND entity IN (" . getEntity("mjobs", 1) . ")";
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
				$line = new ContactLine();


				$line->id				= $obj->rowid;
				$line->ref				= $obj->rowid;
				$line->ref_ext			= $obj->ref_ext;
				$line->civility_id		= $obj->civility_id;
				$line->civility_code	= $obj->civility_id;
				$line->lastname			= $obj->lastname;
				$line->firstname		= $obj->firstname;
				$line->address			= $obj->address;
				$line->zip				= $obj->zip;
				$line->town				= $obj->town;

				$line->fk_departement	= $obj->fk_departement;    // deprecated
				$line->state_id			= $obj->fk_departement;
				$line->departement_code = $obj->state_code;	       // deprecated
				$line->state_code       = $obj->state_code;
				$line->departement		= $obj->state;	           // deprecated
				$line->state			= $obj->state;

				$line->country_id 		= $obj->country_id;
				$line->country_code		= $obj->country_id?$obj->country_code:'';
				$line->country			= $obj->country_id?($langs->trans('Country'.$obj->country_code)!='Country'.$obj->country_code?$langs->transnoentities('Country'.$obj->country_code):$obj->country):'';

				$line->socid			= $obj->fk_soc;
				$line->socname			= $obj->socname;
				$line->poste			= $obj->poste;
				$line->statut			= $obj->statut;

				$line->phone_pro		= trim($obj->phone);
				$line->fax				= trim($obj->fax);
				$line->phone_perso		= trim($obj->phone_perso);
				$line->phone_mobile		= trim($obj->phone_mobile);

				$line->email			= $obj->email;
				$line->jabberid			= $obj->jabberid;
        		$line->skype			= $obj->skype;
                $line->photo			= $obj->photo;
				$line->priv				= $obj->priv;
				$line->mail				= $obj->email;

				$line->birthday			= $this->db->jdate($obj->birthday);
				$line->note				= $obj->note_private;		// deprecated
				$line->note_private		= $obj->note_private;
				$line->note_public		= $obj->note_public;
				$line->default_lang		= $obj->default_lang;
				$line->no_email			= $obj->no_email;
				$line->user_id			= $obj->user_id;
				$line->user_login		= $obj->user_login;
				$line->canvas			= $obj->canvas;

				$line->import_key		= $obj->import_key;

				// Define gender according to civility
				$this->setGenderFromCivility();

				// Search Dolibarr user linked to this contact
				$sql = "SELECT u.rowid ";
				$sql .= " FROM ".MAIN_DB_PREFIX."user as u";
				$sql .= " WHERE u.fk_socpeople = ". $line->id;

				$resqla=$this->db->query($sql);
				if ($resqla)
				{
					if ($this->db->num_rows($resqla))
					{
						$uobj = $this->db->fetch_object($resqla);

						$line->user_id = $uobj->rowid;
					}
				}
				else
				{
					$this->error=$this->db->error();
					return -1;
				}

				// Charge alertes du user
				if ($user)
				{
					$sql = "SELECT fk_user";
					$sql .= " FROM ".MAIN_DB_PREFIX."user_alert";
					$sql .= " WHERE fk_user = ".$user->id." AND fk_contact = ".$this->db->escape($id);

					$resqlb=$this->db->query($sql);
					if ($resqlb)
					{
						if ($this->db->num_rows($resqlb))
						{
							$obj = $this->db->fetch_object($resqlb);

							$line->birthday_alert = 1;
						}
					}
					else
					{
						$this->error=$this->db->error();
						return -1;
					}
				}

				// Retreive all extrafield for contact
                // fetch optionals attributes and labels
                require_once(DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php');
                $extrafields=new ExtraFields($this->db);
                $extralabels=$extrafields->fetch_name_optionals_label($this->table_element,true);
               	$this->fetch_optionals($this->id,$extralabels);

				$this->lines[$line->id] = $line;
			}
			$this->db->free($resql);
			return $num;
		}
		else
		{
			$this->error=$this->db->error();
			return -1;
		}
	}
}


/**
 * Class MjobsLine
 */
class ContactLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */

	public $entity;
	public $ref;
	public $date_create = '';
	public $fk_work_request;
	public $fk_soc;
	public $fk_member;
	public $fk_charge;
	public $fk_departament;
	public $fk_equipment;
	public $fk_property;
	public $fk_location;
	public $fk_type_repair;
	public $email;
	public $internal;
	public $speciality;
	public $detail_problem;
	public $address_ip;
	public $fk_departament_assign;
	public $fk_user_assign;
	public $date_assign = '';
	public $speciality_assign;
	public $description_assign;
	public $description_prog;
	public $date_ini_prog = '';
	public $date_fin_prog = '';
	public $speciality_prog;
	public $fk_equipment_prog;
	public $fk_property_prog;
	public $fk_location_prog;
	public $typemant_prog;
	public $fk_user_prog;
	public $date_ini = '';
	public $date_fin = '';
	public $speciality_job;
	public $typemant;
	public $description_job;
	public $group_task;
	public $task;
	public $image_ini;
	public $image_fin;
	public $tokenreg;
	public $fk_user_create;
	public $fk_user_mod;
	public $datec = '';
	public $datem = '';
	public $tms = '';
	public $status;
	public $description_confirm;
	public $statut_job;

	/**
	 * @var mixed Sample line property 2
	 */

}

?>