<?php

include_once(DOL_DOCUMENT_ROOT.'/adherents/class/adherent.class.php');

class Adherentext extends Adherent
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
		$sql.= " , p.lastname AS plastname, p.lastnametwo AS plastnametwo, p.firstname AS pfirstname, p.docum";
		$sql.= " FROM ".MAIN_DB_PREFIX."adherent_type as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."adherent as d ON d.fk_adherent_type = t.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_country as c ON d.country = c.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."c_departements as dep ON d.state_id = dep.rowid";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."user as u ON d.rowid = u.fk_member";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."p_user as p ON t.rowid = p.fk_user";
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
		//echo '<hr>'.$sql;exit;
		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql))
			{
				$line = new AdherentDefLines();

				$line->id = $obj->rowid;

				$line->entity			= $obj->entity;
				$line->ref				= $obj->rowid;
				$line->id				= $obj->rowid;
				$line->ref_ext			= $obj->ref_ext;
				$line->civility_id		= $obj->civility_id;
				$line->firstname		= $obj->firstname;
				$line->lastname			= $obj->lastname;
				$line->plastnametwo		= $obj->plastnametwo;
				$line->pfirstname		= $obj->pfirstname;
				$line->plastname		= $obj->plastname;
				$line->docum			= $obj->docum;
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

class AdherentDefLines
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