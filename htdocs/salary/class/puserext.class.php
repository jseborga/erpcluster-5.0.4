<?php
require_once DOL_DOCUMENT_ROOT.'/salary/class/puser.class.php';

class Puserext extends Puser
{
	/**
	 *	Return full name (civility+' '+name+' '+lastname)
	 *
	 *	@param	Translate	$langs			Language object for translation of civility
	 *	@param	int			$option			0=No option, 1=Add civility
	 * 	@param	int			$nameorder		-1=Auto, 0=Lastname+Firstname, 1=Firstname+Lastname
	 * 	@param	int			$maxlen			Maximum length
	 * 	@return	string						String with full name
	*/
	function getFullName($langs,$option=0,$nameorder=-1,$maxlen=0)
	{
		global $conf;

	  	//print "lastname=".$this->lastname." name=".$this->name." nom=".$this->nom."<br>\n";
		$lastname=$this->lastname;
		$lastnametwo=$this->lastnametwo;
		$firstname=$this->firstname;
		if (empty($lastname))
			$lastname=($this->lastname?$this->lastname:($this->name?$this->name:$this->nom));
		$lastname = $lastname.' '.$lastnametwo;
		if (empty($firstname)) $firstname=$this->firstname;

		$ret='';
		if ($option && $this->civilite_id)
		{
			if ($langs->transnoentitiesnoconv("Civility".$this->civilite_id)!="Civility".$this->civilite_id) $ret.=$langs->transnoentitiesnoconv("Civility".$this->civilite_id).' ';
			else $ret.=$this->civilite_id.' ';
		}

		$ret.=dolGetFirstLastname($firstname, $lastname, $nameorder);

		return dol_trunc($ret,$maxlen);
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function fetch_user($fk_user='',$login='',$docum='')
	{
		global $langs,$conf;
		if (empty($fk_user) && empty($login) && empty($docum))
			return;

		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_user,";
		$sql.= " t.firstname,";
		$sql.= " t.lastname,";
		$sql.= " t.lastnametwo,";
		$sql.= " t.docum,";
		$sql.= " t.registration,";
		$sql.= " t.sex,";
		$sql.= " t.state_marital,";
		$sql.= " t.issued_in,";
		$sql.= " t.phone_emergency,";
		$sql.= " t.blood_type,";
		$sql.= " t.dependents,";
		$sql.= " r.login ";


		$sql.= " FROM ".MAIN_DB_PREFIX."p_user as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."adherent AS r ";
		$sql.= " ON t.fk_user = r.rowid ";
		$sql.= " WHERE r.entity = ".$conf->entity;
		if ($fk_user)
			$sql.= " AND t.fk_user = ".$fk_user;
		if ($login)
			$sql.= " AND t.fk_login = '".$login."' ";
		if ($docum)
			$sql.= " AND t.docum = '".$docum."' ";

		dol_syslog(get_class($this)."::fetch_user sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;

				$this->fk_user = $obj->fk_user;
				$this->firstname = $obj->firstname;
				$this->lastname = $obj->lastname;
				$this->lastnametwo = $obj->lastnametwo;
				$this->docum = $obj->docum;
				$this->registration = $obj->registration;
				$this->sex = $obj->sex;
				$this->state_marital = $obj->state_marital;
				$this->issued_in = $obj->issued_in;
				$this->phone_emergency = $obj->phone_emergency;
				$this->blood_type = $obj->blood_type;
				$this->dependents = $obj->dependents;


			}
			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_user ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *    	Renvoie nom clicable (avec eventuellement le picto)
	 *
	 *		@param	int		$withpicto		0=Pas de picto, 1=Inclut le picto dans le lien, 2=Picto seul
	 *		@param	int		$maxlen			length max libelle
	 *		@param	string	$option			Page lien
	 *		@return	string					Chaine avec URL
	 */
	function getNomUrlx($withpicto=0,$maxlen=0,$option='card')
	{
		global $langs;

		$result='';

		if ($option == 'card')
		{
			$lien = '<a href="'.DOL_URL_ROOT.'/salary/user/fiche.php?rowid='.$this->id.'">';
			$lienfin='</a>';
		}
		if ($option == 'subscription')
		{
			$lien = '<a href="'.DOL_URL_ROOT.'/adherents/card_subscriptions.php?rowid='.$this->id.'">';
			$lienfin='</a>';
		}
		if ($option == 'category')
		{
			$lien = '<a href="'.DOL_URL_ROOT.'/categories/categorie.php?id='.$this->id.'&type=3">';
			$lienfin='</a>';
		}

		$picto='user';
		$label=$langs->trans("ShowMember");

		if ($withpicto) $result.=($lien.img_object($label,$picto).$lienfin);
		if ($withpicto && $withpicto != 2) $result.=' ';
		$result.=$lien.($maxlen?dol_trunc($this->ref,$maxlen):$this->ref).$lienfin;
		return $result;
	}

}
?>