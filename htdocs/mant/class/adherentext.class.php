<?php
require_once DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php';

class Adherentext extends Adherent
{
	/**
	*	Load member from database
	*
	*	@param	int		$rowid      Id of object to load
	* 	@param	string	$ref		To load member from its ref
	* 	@param	int		$fk_soc		To load member from its link to third party
	* 	@param	string	$ref_ext	External reference
	*	@return int         		>0 if OK, 0 if not found, <0 if KO
	*/
	function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND',$filterstatic='',$lView=false)
	{
		global $langs;

		$sql = "SELECT d.rowid, d.ref_ext, d.civility as civility_id, d.firstname, d.lastname, d.societe as company, d.fk_soc, d.statut, d.public, d.address, d.zip, d.town, d.note_private,";
		$sql.= " d.note_public,";
		$sql.= " d.email, d.skype, d.phone, d.phone_perso, d.phone_mobile, d.login, d.pass, d.pass_crypted,";
		$sql.= " d.photo, d.fk_adherent_type, d.morphy, d.entity,";
		$sql.= " d.datec as datec,";
		$sql.= " d.tms as datem,";
		$sql.= " d.datefin as datefin,";
		$sql.= " d.birth as birthday,";
		$sql.= " d.datevalid as datev,";
		$sql.= " d.country,";
		$sql.= " d.state_id,";
		$sql.= " d.model_pdf,";
		$sql.= " c.rowid as country_id, c.code as country_code, c.label as country,";
		$sql.= " dep.nom as state, dep.code_departement as state_code,";
		$sql.= " t.libelle as type, t.subscription as subscription,";
		$sql.= " u.rowid as user_id, u.login as user_login";
		$sql.= " FROM ".MAIN_DB_PREFIX."adherent_type as t, ".MAIN_DB_PREFIX."adherent as d";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_country as c ON d.country = c.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_departements as dep ON d.state_id = dep.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."user as u ON d.rowid = u.fk_member";

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		$sql.= ' WHERE 1 = 1';
		$sql .= " AND d.entity IN (" . getEntity() . ")";
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


		dol_syslog(get_class($this)."::fetchAll", LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$i = 0;
			$num = $this->db->num_rows($resql);
			if ($this->db->num_rows($resql))
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$line = new Adherent($this->db);
					$line->entity			= $obj->entity;
					$line->ref				= $obj->rowid;
					$line->id				= $obj->rowid;
					$line->ref_ext			= $obj->ref_ext;
					$line->civility_id		= $obj->civility_id;
					$line->firstname		= $obj->firstname;
					$line->lastname			= $obj->lastname;
					$line->login			= $obj->login;
					$line->societe			= $obj->company;
					$line->company			= $obj->company;
					$line->fk_soc			= $obj->fk_soc;
					$line->address			= $obj->address;
					$line->zip				= $obj->zip;
					$line->town				= $obj->town;

					$line->pass				= $obj->pass;
					$line->pass_indatabase  = $obj->pass;
					$line->pass_indatabase_crypted = $obj->pass_crypted;

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
					$line->note_public		= $obj->note_public;
					$line->morphy			= $obj->morphy;

					$line->typeid			= $obj->fk_adherent_type;
					$line->type				= $obj->type;
					$line->need_subscription 	= $obj->subscription;

					$line->user_id			= $obj->user_id;
					$line->user_login		= $obj->user_login;

					$line->model_pdf        = $obj->model_pdf;

								// Retreive all extrafield for thirdparty
								// fetch optionals attributes and labels
					require_once DOL_DOCUMENT_ROOT.'/core/class/extrafields.class.php';
					$extrafields=new ExtraFields($this->db);
					$extralabels=$extrafields->fetch_name_optionals_label($this->table_element,true);
					$line->fetch_optionals($line->id,$extralabels);

								// Load other properties
					$result=$line->fetch_subscriptions();
					$this->lines[] = $line;
					$i++;
				}
				$this->db->free($resql);
				return $num;
			}
			else
			{
				return 0;
			}
		}
		else
		{
			$this->error=$this->db->lasterror();
			return -1;
		}
	}
}
?>