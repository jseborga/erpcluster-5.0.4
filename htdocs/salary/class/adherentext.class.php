<?php

include_once(DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php');

class Adherentext extends Adherent
{

    /**
     *	Update a member in database (standard information and password)
     *
     *	@param	User	$user				User making update
     *	@param	int		$notrigger			1=disable trigger UPDATE (when called by create)
     *	@param	int		$nosyncuser			0=Synchronize linked user (standard info), 1=Do not synchronize linked user
     *	@param	int		$nosyncuserpass		0=Synchronize linked user (password), 1=Do not synchronize linked user
     *	@param	int		$nosyncthirdparty	0=Synchronize linked thirdparty (standard info), 1=Do not synchronize linked thirdparty
     * 	@param	string	$action				Current action for hookmanager
     * 	@return	int							<0 if KO, >0 if OK
     */
    function updateadd($user,$notrigger=0,$nosyncuser=0,$nosyncuserpass=0,$nosyncthirdparty=0,$action='update')
    {
        global $conf, $langs, $hookmanager;

        $nbrowsaffected=0;
        $error=0;

        dol_syslog(get_class($this)."::update notrigger=".$notrigger.", nosyncuser=".$nosyncuser.", nosyncuserpass=".$nosyncuserpass." nosyncthirdparty=".$nosyncthirdparty.", email=".$this->email);

        // Clean parameters
		$this->lastname=trim($this->lastname)?trim($this->lastname):trim($this->lastname);
		$this->firstname=trim($this->firstname)?trim($this->firstname):trim($this->firstname);
		$this->address=($this->address?$this->address:$this->address);
		$this->zip=($this->zip?$this->zip:$this->zip);
		$this->town=($this->town?$this->town:$this->town);
		$this->country_id=($this->country_id > 0?$this->country_id:$this->country_id);
		$this->state_id=($this->state_id > 0?$this->state_id:$this->state_id);
		if (! empty($conf->global->MAIN_FIRST_TO_UPPER)) $this->lastname=ucwords(trim($this->lastname));
		if (! empty($conf->global->MAIN_FIRST_TO_UPPER)) $this->firstname=ucwords(trim($this->firstname));
		$this->note_public=($this->note_public?$this->note_public:$this->note_public);
		$this->note_private=($this->note_private?$this->note_private:$this->note_private);

        // Check parameters
        if (! empty($conf->global->ADHERENT_MAIL_REQUIRED) && ! isValidEMail($this->email))
        {
            $langs->load("errors");
            $this->error = $langs->trans("ErrorBadEMail",$this->email);
            return -1;
        }

        $this->db->begin();

        $sql = "UPDATE ".MAIN_DB_PREFIX."adherent SET";
        $sql.= " civility = ".(!is_null($this->civility_id)?"'".$this->civility_id."'":"null");
        $sql.= ", firstname = ".($this->firstname?"'".$this->db->escape($this->firstname)."'":"null");
        $sql.= ", lastname=" .($this->lastname?"'".$this->db->escape($this->lastname)."'":"null");
        $sql.= ", login="   .($this->login?"'".$this->db->escape($this->login)."'":"null");
        $sql.= ", societe=" .($this->societe?"'".$this->db->escape($this->societe)."'":"null");
        $sql.= ", fk_soc="  .($this->fk_soc > 0?"'".$this->fk_soc."'":"null");
        $sql.= ", address=" .($this->address?"'".$this->db->escape($this->address)."'":"null");
        $sql.= ", zip="      .($this->zip?"'".$this->db->escape($this->zip)."'":"null");
        $sql.= ", town="   .($this->town?"'".$this->db->escape($this->town)."'":"null");
        $sql.= ", country=".($this->country_id>0?"'".$this->country_id."'":"null");
        $sql.= ", state_id=".($this->state_id>0?"'".$this->state_id."'":"null");
        $sql.= ", email='".$this->db->escape($this->email)."'";
        $sql.= ", skype='".$this->db->escape($this->skype)."'";
        $sql.= ", phone="   .($this->phone?"'".$this->db->escape($this->phone)."'":"null");
        $sql.= ", phone_perso=" .($this->phone_perso?"'".$this->db->escape($this->phone_perso)."'":"null");
        $sql.= ", phone_mobile=" .($this->phone_mobile?"'".$this->db->escape($this->phone_mobile)."'":"null");
        $sql.= ", note_private=" .($this->note_private?"'".$this->db->escape($this->note_private)."'":"null");
        $sql.= ", note_public=" .($this->note_public?"'".$this->db->escape($this->note_public)."'":"null");
        $sql.= ", photo="   .($this->photo?"'".$this->photo."'":"null");
        $sql.= ", public='".$this->db->escape($this->public)."'";
        $sql.= ", statut="  .$this->statut;
        $sql.= ", fk_adherent_type=".$this->typeid;
        $sql.= ", morphy='".$this->db->escape($this->morphy)."'";
        $sql.= ", birth="   .($this->birth?"'".$this->db->idate($this->birth)."'":"null");
        if ($this->datefin)   $sql.= ", datefin='".$this->db->idate($this->datefin)."'";		// Must be modified only when deleting a subscription
        if ($this->datevalid) $sql.= ", datevalid='".$this->db->idate($this->datevalid)."'";	// Must be modified only when validating a member
        $sql.= ", fk_user_mod=".($user->id>0?$user->id:'null');	// Can be null because member can be create by a guest
        $sql.= " WHERE rowid = ".$this->id;

        dol_syslog(get_class($this)."::update update member", LOG_DEBUG);
        $resql = $this->db->query($sql);
        if ($resql)
        {
		    unset($this->country_code);
		    unset($this->country);
		    unset($this->state_code);
		    unset($this->state);

		    $nbrowsaffected+=$this->db->affected_rows($resql);

		    $action='update';


            if (! $error)
            {
                $this->db->commit();
                return $nbrowsaffected;
            }
            else
            {
                $this->db->rollback();
                return -1;
            }
        }
        else
        {
            $this->db->rollback();
            $this->error=$this->db->lasterror();
            return -2;
        }
    }


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
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(),$filtermode='AND',$filterstatic= '',$lRow=false)
	{
		global $langs;

		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = "SELECT d.rowid, d.ref_ext, d.civility as civility_id, d.firstname, d.lastname, d.societe as company, d.fk_soc, d.statut, d.public, d.address, d.zip, d.town, d.note_private,";
		$sql.= " d.note_public,";
		$sql.= " d.email, d.skype, d.phone, d.phone_perso, d.phone_mobile, d.login, d.pass,";
		$sql.= " d.photo, d.fk_adherent_type, d.morphy, d.entity,";
		$sql.= " d.datec as datec,";
		$sql.= " d.tms as datem,";
		$sql.= " d.datefin as datefin,";
		$sql.= " d.birth as birthday,";
		$sql.= " d.datevalid as datev,";
		$sql.= " d.country,";
		$sql.= " d.state_id,";
		$sql.= " c.rowid as country_id, c.code as country_code, c.label as country,";
		$sql.= " dep.nom as state, dep.code_departement as state_code,";
		$sql.= " t.libelle as type, ";
		$sql.= " u.rowid as user_id, u.login as user_login";
		$sql.= " , p.lastname AS plastname, p.lastnametwo AS plastnametwo, p.firstname AS pfirstname, p.docum, p.sex ";
        $sql.= " , at.libelle AS label_adherent_type ";
		$sql.= " FROM ".MAIN_DB_PREFIX."adherent_type as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."adherent as d ON d.fk_adherent_type = t.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_country as c ON d.country = c.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_departements as dep ON d.state_id = dep.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."user as u ON d.rowid = u.fk_member";
        $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."adherent_type as at ON d.fk_adherent_type = at.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_user as p ON d.rowid = p.fk_user";
	  //$sql.= " WHERE ";

	  // Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' WHERE ' . implode(' '.$filtermode.' ', $sqlwhere);
		}
		if (!empty($filterstatic))
			$sql.= $filterstatic;

		if (!empty($sortfield)) {
			$sql .= ' ORDER BY ' . $sortfield . ' ' . $sortorder;
		}
		if (!empty($limit)) {
			$sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();
		//echo '<hr>'.$sql;
		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql))
			{
				$line = new AssistancedefLines();

				$line->id = $obj->rowid;

				$line->entity			= $obj->entity;
				$line->ref				= $obj->rowid;
				$line->id				= $obj->rowid;
				$line->ref_ext			= $obj->ref_ext;
				$line->civility_id		= $obj->civility_id;
				$line->firstname		= $obj->firstname;
				$line->lastname			= $obj->lastname;
                $line->pfirstname        = $obj->pfirstname;
                $line->plastname         = $obj->plastname;

                $line->plastnametwo      = $obj->plastnametwo;
                $line->sex         = $obj->sex;
				$line->login			= $obj->login;
				$line->pass				= $obj->pass;
				$line->societe			= $obj->company;
				$line->company			= $obj->company;
				$line->fk_soc			= $obj->fk_soc;
				$line->address			= $obj->address;
				$line->zip				= $obj->zip;
				$line->town				= $obj->town;

				$line->state_id			= $obj->state_id;
				$line->state_code		= $obj->state_id?$obj->state_code:'';
				$line->state			= $obj->state_id?$obj->state:'';

				$line->country_id		= $obj->country_id;
				$line->country_code		= $obj->country_code;
				if ($langs->trans("Country".$obj->country_code) != "Country".$obj->country_code)
					$line->country = $langs->transnoentitiesnoconv("Country".$obj->country_code);
				else
					$line->country=$obj->country;

				$line->phone			= $obj->phone;
				$line->phone_perso		= $obj->phone_perso;
				$line->phone_mobile		= $obj->phone_mobile;
				$line->email			= $obj->email;
				$line->skype			= $obj->skype;

				$line->photo			= $obj->photo;
				$line->statut			= $obj->statut;
				$line->public			= $obj->public;

				$line->datec			= $this->db->jdate($obj->datec);
				$line->datem			= $this->db->jdate($obj->datem);
				$line->datefin			= $this->db->jdate($obj->datefin);
				$line->datevalid		= $this->db->jdate($obj->datev);
				$line->birth			= $this->db->jdate($obj->birthday);

				$line->note_private		= $obj->note_private;
				$line->note_public      = $obj->note_public;
				$line->morphy			= $obj->morphy;
                $line->label_adherent_type = $obj->label_adherent_type;

				$line->typeid			= $obj->fk_adherent_type;
				$line->type				= $obj->type;
				$line->need_subscription = ($obj->cotisation=='yes'?1:0);

				$line->user_id			= $obj->user_id;
				$line->user_login		= $obj->user_login;
				$this->lines[] = $line;
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

class AssistancedefLines
{
	var	$id;
	var $entity;
	var $ref;
	var $ref_ext;
	var $civility_id;
	var $firstname;
	var $lastname;
	var $login;
	var $pass;
	var $societe;
	var $company;
	var $fk_soc;
	var $address;
	var $zip;
	var $town;

	var $state_id;
	var $state_code;
	var $state;

	var $country_id;
	var $country_code;
	var $country;

	var $phone;
	var $phone_perso;
	var $phone_mobile;
	var $email;
	var $skype;

	var $photo;
	var $statut;
	var $public;

	var $datec;
	var $datem;
	var $datefin;
	var $datevalid;
	var $birth;

	var $note_private;
	var $note_public;
	var $morphy;

	var $typeid;
	var $type;
	var $need_subscription;

	var $user_id;
	var $user_login;
}
?>