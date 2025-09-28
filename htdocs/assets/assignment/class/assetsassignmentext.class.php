<?php
require_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignment.class.php';

class Assetsassignmentext extends Assetsassignment
{

	/**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatut($mode=0)
	{
		return $this->LibStatut($this->status,$mode);
	}

	/**
	 *  Return the status
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 5=Long label + Picto
	 *  @return string 			       	Label of status
	 */
	static function LibStatut($status,$mode=0)
	{
		global $langs;

		if ($mode == 0)
		{
			$prefix='';
			if ($status == 3) return $langs->trans('Assignment');
			if ($status == 2) return $langs->trans('Approved');
			if ($status == 1) return $langs->trans('Validated');
			if ($status == 0) return $langs->trans('Draft');
		}
		if ($mode == 1)
		{
			if ($status == 1) return $langs->trans('Solicitado');
			if ($status == 0) return $langs->trans('Draft');
		}
		if ($mode == 2)
		{
			if ($status == 3) return img_picto($langs->trans('Accepted'),'statut3').' '.$langs->trans('Accepted');
			if ($status == 2) return img_picto($langs->trans('Validated'),'statut2').' '.$langs->trans('Validated');
			if ($status == 1) return img_picto($langs->trans('Solicitado'),'statut1').' '.$langs->trans('Solicitado');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut0').' '.$langs->trans('Draft');
		}
		if ($mode == 3)
		{
			if ($status == 2) return img_picto($langs->trans('Validated'),'statut4');
			if ($status == 1) return img_picto($langs->trans('Solicitado'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut5');
		}
		if ($mode == 4)
		{
			if ($status == 2) return img_picto($langs->trans('Validated'),'statut4').' '.$langs->trans('Validated');
			if ($status == 1) return img_picto($langs->trans('Solicitado'),'statut4').' '.$langs->trans('Solicitado');
			if ($status == 0) return img_picto($langs->trans('Draft'),'statut5').' '.$langs->trans('Draft');
		}
		if ($mode == 5)
		{
			if ($status == 2) return $langs->trans('Validated').' '.img_picto($langs->trans('Validated'),'statut4');
			if ($status == 1) return $langs->trans('Solicitado').' '.img_picto($langs->trans('Solicitado'),'statut4');
			if ($status == 0) return $langs->trans('Draft').' '.img_picto($langs->trans('Draft'),'statut5');
		}
		if ($mode == 6)
		{
			if ($status == 2) return $langs->trans('Validated').' '.img_picto($langs->trans('Validated'),'statut4');
			if ($status == 1) return $langs->trans('Solicitado').' '.img_picto($langs->trans('Solicitado'),'statut4');
			if ($status == 0) return $langs->trans('Draft').' '.img_picto($langs->trans('Draft'),'statut5');
		}
	}
		/**
	 *  Create a document onto disk according to template model.
	 *
	 *  @param	    string		$modele			Force template to use ('' to not force)
	 *  @param		Translate	$outputlangs	Object lang to use for traduction
	 *  @param      int			$hidedetails    Hide details of lines
	 *  @param      int			$hidedesc       Hide description
	 *  @param      int			$hideref        Hide ref
	 *  @return     int          				0 if KO, 1 if OK
	 */
	public function generateDocument($modele, $outputlangs, $hidedetails=0, $hidedesc=0, $hideref=0)
	{
		global $conf, $user, $langs;

		$langs->load("suppliers");

		// Sets the model on the model name to use
		if (! dol_strlen($modele))
		{
			if (! empty($conf->global->ASSETS_ADDON_PDF))
			{
				$modele = $conf->global->ASSETS_ADDON_PDF;
			}
			else
			{
				$modele = 'fractalbeenassets';
			}
		}

		$modelpath = "assets/core/modules/doc/";

		return $this->commonGenerateDocument($modelpath, $modele, $outputlangs, $hidedetails, $hidedesc, $hideref);
	}

		//MODIFICADO
	/**
	 *  Returns the reference to the following non used Order depending on the active numbering module
	 *  defined into ALMACEN_ADDON
	 *
	 *  @param	Societe		$soc  	Object thirdparty
	 *  @return string      		Order free reference
	 */
	function getNextNumRef($soc)
	{
		global $db, $langs, $conf;
		$langs->load("assets@assets");
	//modelo fijo de numeracion;
		$modelnum = 'mod_assets_ubuntubo_assign';
		$dir = DOL_DOCUMENT_ROOT . "/assets/core/modules";

		//if (! empty($conf->global->ASSETS_ADDON))
		if (! empty($modelnum))
		{
			//$file = $conf->global->ASSETS_ADDON.".php";
			$file = $modelnum.".php";
			// Chargement de la classe de numerotation
		//$classname = $conf->global->ASSETS_ADDON;
			$classname = $modelnum;
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
					dol_print_error($db,"Assetsassignment::getNextNumRef ".$obj->error);
					return "";
				}
			}
			else
			{
				print $langs->trans("Error")." ".$langs->trans("Error_ASSETS_ADDON_NotDefined");
				return "";
			}
		}
		else
		{
			print $langs->trans("Error")." ".$langs->trans("Error_ASSETS_ADDON_NotDefined");
			return "";
		}
	}

	/**
	 *  Load all detailed lines into this->lines
	 *
	 *  @return     int         1 if OK, < 0 if KO
	 */
	function fetch_lines()
	{
		global $langs,$conf;
		$this->lines=array();

		$sql = "SELECT ";
		$sql .= ' t.rowid,';
		$sql .= " t.fk_asset_assignment,";
		$sql .= " t.fk_asset,";
		$sql .= " t.date_assignment,";
		$sql .= " t.date_end,";
		$sql .= " t.date_create,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.date_mod,";
		$sql .= " t.detail,";
		$sql .= " t.been,";
		$sql .= " t.tms,";
		$sql .= " t.active,";
		$sql .= " t.status";

		$sql.= " , p.descrip, p.ref, p.descrip AS description, p.descrip";
		$sql.= " FROM ".MAIN_DB_PREFIX."assets_assignment as a";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."assets_assignment_det AS t ON t.fk_asset_assignment = a.rowid";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."assets AS p ON t.fk_asset = p.rowid";
		$sql.= ' WHERE t.fk_asset_assignment ='.$this->id;
		$sql.= $this->db->order("p.ref");

		dol_syslog(get_class($this).'::fetch_lines sql='.$sql, LOG_DEBUG);
		$result = $this->db->query($sql);
		$this->lines = array();
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;
			include_once DOL_DOCUMENT_ROOT.'/assets/assignment/class/assetsassignmentdetext.class.php';
			while ($i < $num)
			{
				$obj = $this->db->fetch_object($result);

				$line = new Assetsassignmentdetext($this->db);

				$line->id = $obj->rowid;
				$line->fk_asset_assignment = $obj->fk_asset_assignment;
				$line->fk_asset = $obj->fk_asset;
				$line->date_assignment = $this->db->jdate($obj->date_assignment);
				$line->date_end = $this->db->jdate($obj->date_end);
				$line->date_create = $this->db->jdate($obj->date_create);
				$line->fk_user_create = $obj->fk_user_create;
				$line->fk_user_mod = $obj->fk_user_mod;
				$line->date_mod = $this->db->jdate($obj->date_mod);
				$line->detail = $obj->detail;
				$line->been = $obj->been;
				$line->tms = $this->db->jdate($obj->tms);
				$line->active = $obj->active;
				$line->status = $obj->status;
				$line->descrip = $obj->descrip;
				$line->ref = $obj->ref;

				$this->lines[$i] = $line;

				$i++;
			}
			$this->db->free($result);
			return 1;
		}
		else
		{
			$this->error=$this->db->error();
			dol_syslog(get_class($this).'::fetch_lines '.$this->error,LOG_ERR);
			return -3;
		}
	}
}
?>