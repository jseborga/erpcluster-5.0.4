<?php
require_once DOL_DOCUMENT_ROOT.'/poa/class/poapoa.class.php';
class Poapoaext extends Poapoa
{
		//modificado
	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlist($fk_father)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_structure,";
		$sql.= " t.ref,";
		$sql.= " t.sigla,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.classification,";
		$sql.= " t.source_verification,";
		$sql.= " t.unit,";
		$sql.= " t.responsible_one,";
		$sql.= " t.responsible_two,";
		$sql.= " t.responsible,";
		$sql.= " t.m_jan,";
		$sql.= " t.m_feb,";
		$sql.= " t.m_mar,";
		$sql.= " t.m_apr,";
		$sql.= " t.m_may,";
		$sql.= " t.m_jun,";
		$sql.= " t.m_jul,";
		$sql.= " t.m_aug,";
		$sql.= " t.m_sep,";
		$sql.= " t.m_oct,";
		$sql.= " t.m_nov,";
		$sql.= " t.m_dec,";
		$sql.= " t.p_jan,";
		$sql.= " t.p_feb,";
		$sql.= " t.p_mar,";
		$sql.= " t.p_apr,";
		$sql.= " t.p_may,";
		$sql.= " t.p_jun,";
		$sql.= " t.p_jul,";
		$sql.= " t.p_aug,";
		$sql.= " t.p_sep,";
		$sql.= " t.p_oct,";
		$sql.= " t.p_nov,";
		$sql.= " t.p_dec,";
		$sql.= " t.fk_area,";
		$sql.= " t.weighting,";
		$sql.= " t.fk_poa_reformulated,";
		$sql.= " t.version,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.statut_ref";



		$sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as t";
		$sql.= " WHERE t.fk_father = ".$fk_father;

		dol_syslog(get_class($this)."::getlist sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				$array = array();
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objNew = new Poapoa($this->db);
					$objNew->id    = $obj->rowid;

					$objNew->entity = $obj->entity;
					$objNew->gestion = $obj->gestion;
					$objNew->fk_structure = $obj->fk_structure;
					$objNew->ref = $obj->ref;
					$objNew->sigla = $obj->sigla;
					$objNew->label = $obj->label;
					$objNew->pseudonym = $obj->pseudonym;
					$objNew->partida = $obj->partida;
					$objNew->amount = $obj->amount;
					$objNew->classification = $obj->classification;
					$objNew->source_verification = $obj->source_verification;
					$objNew->unit = $obj->unit;
					$objNew->responsible_one = $obj->responsible_one;
					$objNew->responsible_two = $obj->responsible_two;
					$objNew->responsible = $obj->responsible;
					$objNew->m_jan = $obj->m_jan;
					$objNew->m_feb = $obj->m_feb;
					$objNew->m_mar = $obj->m_mar;
					$objNew->m_apr = $obj->m_apr;
					$objNew->m_may = $obj->m_may;
					$objNew->m_jun = $obj->m_jun;
					$objNew->m_jul = $obj->m_jul;
					$objNew->m_aug = $obj->m_aug;
					$objNew->m_sep = $obj->m_sep;
					$objNew->m_oct = $obj->m_oct;
					$objNew->m_nov = $obj->m_nov;
					$objNew->m_dec = $obj->m_dec;
					$objNew->p_jan = $obj->p_jan;
					$objNew->p_feb = $obj->p_feb;
					$objNew->p_mar = $obj->p_mar;
					$objNew->p_apr = $obj->p_apr;
					$objNew->p_may = $obj->p_may;
					$objNew->p_jun = $obj->p_jun;
					$objNew->p_jul = $obj->p_jul;
					$objNew->p_aug = $obj->p_aug;
					$objNew->p_sep = $obj->p_sep;
					$objNew->p_oct = $obj->p_oct;
					$objNew->p_nov = $obj->p_nov;
					$objNew->p_dec = $obj->p_dec;
					$objNew->fk_area = $obj->fk_area;
					$objNew->weighting = $obj->weighting;
					$objNew->fk_poa_reformulated = $obj->fk_poa_reformulated;
					$objNew->version = $obj->version;
					$objNew->fk_user_create = $obj->fk_user_create;
					$objNew->fk_user_mod = $obj->fk_user_mod;
					$objNew->datec = $this->db->jdate($obj->datec);
					$objNew->datem = $this->db->jdate($obj->datem);
					$objNew->tms = $this->db->jdate($obj->tms);
					$objNew->statut = $obj->statut;
					$objNew->statut_ref = $obj->statut_ref;


					$array[$obj->rowid] = $objNew;
					$i++;
				}
				return $array;
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlist_structure($fk_structure,$statut=1)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_structure,";
		$sql.= " t.ref,";
		$sql.= " t.sigla,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.classification,";
		$sql.= " t.source_verification,";
		$sql.= " t.unit,";
		$sql.= " t.responsible_one,";
		$sql.= " t.responsible_two,";
		$sql.= " t.responsible,";
		$sql.= " t.m_jan,";
		$sql.= " t.m_feb,";
		$sql.= " t.m_mar,";
		$sql.= " t.m_apr,";
		$sql.= " t.m_may,";
		$sql.= " t.m_jun,";
		$sql.= " t.m_jul,";
		$sql.= " t.m_aug,";
		$sql.= " t.m_sep,";
		$sql.= " t.m_oct,";
		$sql.= " t.m_nov,";
		$sql.= " t.m_dec,";
		$sql.= " t.p_jan,";
		$sql.= " t.p_feb,";
		$sql.= " t.p_mar,";
		$sql.= " t.p_apr,";
		$sql.= " t.p_may,";
		$sql.= " t.p_jun,";
		$sql.= " t.p_jul,";
		$sql.= " t.p_aug,";
		$sql.= " t.p_sep,";
		$sql.= " t.p_oct,";
		$sql.= " t.p_nov,";
		$sql.= " t.p_dec,";
		$sql.= " t.fk_area,";
		$sql.= " t.weighting,";
		$sql.= " t.fk_poa_reformulated,";
		$sql.= " t.version,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.statut_ref";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as t";
		$sql.= " WHERE t.fk_structure = ".$fk_structure;
		$sql.= " AND t.statut = ".$statut;
		dol_syslog(get_class($this)."::getlist_structure sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objNew = new Poapoa($this->db);
					$objNew->id    = $obj->rowid;

					$objNew->entity = $obj->entity;
					$objNew->gestion = $obj->gestion;
					$objNew->fk_structure = $obj->fk_structure;
					$objNew->ref = $obj->ref;
					$objNew->sigla = $obj->sigla;
					$objNew->label = $obj->label;
					$objNew->pseudonym = $obj->pseudonym;
					$objNew->partida = $obj->partida;
					$objNew->amount = $obj->amount;
					$objNew->classification = $obj->classification;
					$objNew->source_verification = $obj->source_verification;
					$objNew->unit = $obj->unit;
					$objNew->responsible_one = $obj->responsible_one;
					$objNew->responsible_two = $obj->responsible_two;
					$objNew->responsible = $obj->responsible;
					$objNew->m_jan = $obj->m_jan;
					$objNew->m_feb = $obj->m_feb;
					$objNew->m_mar = $obj->m_mar;
					$objNew->m_apr = $obj->m_apr;
					$objNew->m_may = $obj->m_may;
					$objNew->m_jun = $obj->m_jun;
					$objNew->m_jul = $obj->m_jul;
					$objNew->m_aug = $obj->m_aug;
					$objNew->m_sep = $obj->m_sep;
					$objNew->m_oct = $obj->m_oct;
					$objNew->m_nov = $obj->m_nov;
					$objNew->m_dec = $obj->m_dec;
					$objNew->p_jan = $obj->p_jan;
					$objNew->p_feb = $obj->p_feb;
					$objNew->p_mar = $obj->p_mar;
					$objNew->p_apr = $obj->p_apr;
					$objNew->p_may = $obj->p_may;
					$objNew->p_jun = $obj->p_jun;
					$objNew->p_jul = $obj->p_jul;
					$objNew->p_aug = $obj->p_aug;
					$objNew->p_sep = $obj->p_sep;
					$objNew->p_oct = $obj->p_oct;
					$objNew->p_nov = $obj->p_nov;
					$objNew->p_dec = $obj->p_dec;
					$objNew->fk_area = $obj->fk_area;
					$objNew->weighting = $obj->weighting;
					$objNew->fk_poa_reformulated = $obj->fk_poa_reformulated;
					$objNew->version = $obj->version;
					$objNew->fk_user_create = $obj->fk_user_create;
					$objNew->fk_user_mod = $obj->fk_user_mod;
					$objNew->datec = $this->db->jdate($obj->datec);
					$objNew->datem = $this->db->jdate($obj->datem);
					$objNew->tms = $this->db->jdate($obj->tms);
					$objNew->statut = $obj->statut;
					$objNew->statut_ref = $obj->statut_ref;


					$this->array[$obj->rowid] = $objNew;
					$i++;
				}
				return count($this->array);
			}
			$this->db->free($resql);
			return 0;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlistref($fk_poa_reformulated)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_structure,";
		$sql.= " t.ref,";
		$sql.= " t.sigla,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.classification,";
		$sql.= " t.source_verification,";
		$sql.= " t.unit,";
		$sql.= " t.responsible_one,";
		$sql.= " t.responsible_two,";
		$sql.= " t.responsible,";
		$sql.= " t.m_jan,";
		$sql.= " t.m_feb,";
		$sql.= " t.m_mar,";
		$sql.= " t.m_apr,";
		$sql.= " t.m_may,";
		$sql.= " t.m_jun,";
		$sql.= " t.m_jul,";
		$sql.= " t.m_aug,";
		$sql.= " t.m_sep,";
		$sql.= " t.m_oct,";
		$sql.= " t.m_nov,";
		$sql.= " t.m_dec,";
		$sql.= " t.p_jan,";
		$sql.= " t.p_feb,";
		$sql.= " t.p_mar,";
		$sql.= " t.p_apr,";
		$sql.= " t.p_may,";
		$sql.= " t.p_jun,";
		$sql.= " t.p_jul,";
		$sql.= " t.p_aug,";
		$sql.= " t.p_sep,";
		$sql.= " t.p_oct,";
		$sql.= " t.p_nov,";
		$sql.= " t.p_dec,";
		$sql.= " t.fk_area,";
		$sql.= " t.weighting,";
		$sql.= " t.fk_poa_reformulated,";
		$sql.= " t.version,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.statut_ref";



		$sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as t";
		$sql.= " WHERE t.fk_poa_reformulated = ".$fk_poa_reformulated;

		dol_syslog(get_class($this)."::getlistref sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objNew = new Poapoa($this->db);
					$objNew->id    = $obj->rowid;

					$objNew->entity = $obj->entity;
					$objNew->gestion = $obj->gestion;
					$objNew->fk_structure = $obj->fk_structure;
					$objNew->ref = $obj->ref;
					$objNew->sigla = $obj->sigla;
					$objNew->label = $obj->label;
					$objNew->pseudonym = $obj->pseudonym;
					$objNew->partida = $obj->partida;
					$objNew->amount = $obj->amount;
					$objNew->classification = $obj->classification;
					$objNew->source_verification = $obj->source_verification;
					$objNew->unit = $obj->unit;
					$objNew->responsible_one = $obj->responsible_one;
					$objNew->responsible_two = $obj->responsible_two;
					$objNew->responsible = $obj->responsible;
					$objNew->m_jan = $obj->m_jan;
					$objNew->m_feb = $obj->m_feb;
					$objNew->m_mar = $obj->m_mar;
					$objNew->m_apr = $obj->m_apr;
					$objNew->m_may = $obj->m_may;
					$objNew->m_jun = $obj->m_jun;
					$objNew->m_jul = $obj->m_jul;
					$objNew->m_aug = $obj->m_aug;
					$objNew->m_sep = $obj->m_sep;
					$objNew->m_oct = $obj->m_oct;
					$objNew->m_nov = $obj->m_nov;
					$objNew->m_dec = $obj->m_dec;
					$objNew->p_jan = $obj->p_jan;
					$objNew->p_feb = $obj->p_feb;
					$objNew->p_mar = $obj->p_mar;
					$objNew->p_apr = $obj->p_apr;
					$objNew->p_may = $obj->p_may;
					$objNew->p_jun = $obj->p_jun;
					$objNew->p_jul = $obj->p_jul;
					$objNew->p_aug = $obj->p_aug;
					$objNew->p_sep = $obj->p_sep;
					$objNew->p_oct = $obj->p_oct;
					$objNew->p_nov = $obj->p_nov;
					$objNew->p_dec = $obj->p_dec;
					$objNew->fk_area = $obj->fk_area;
					$objNew->weighting = $obj->weighting;
					$objNew->fk_poa_reformulated = $obj->fk_poa_reformulated;
					$objNew->version = $obj->version;
					$objNew->fk_user_create = $obj->fk_user_create;
					$objNew->fk_user_mod = $obj->fk_user_mod;
					$objNew->datec = $this->db->jdate($obj->datec);
					$objNew->datem = $this->db->jdate($obj->datem);
					$objNew->tms = $this->db->jdate($obj->tms);
					$objNew->statut = $obj->statut;
					$objNew->statut_ref = $obj->statut_ref;
					$array[$obj->rowid] = $objNew;
					$i++;
				}
				return $array;
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param	string	$selected       Id or Code or Label of preselected country
	 *  @param  string	$htmlname       Name of html select object
	 *  @param  string	$htmloption     Options html on select object
	 *  @param	string	$maxlength	Max length for labels (0=no limit)
	 *  @param	string	$showempty	View space labels (0=no view)

	 *  @return string           		HTML string with select
	 */
	function select_poa($selected='',$htmlname='fk_poa',$htmloption='',$maxlength=0,$showempty=0,$fk_structure='',$alist='')
	{
		global $conf,$langs;

		if (is_array($alist))
			$filter = " AND c.rowid IN (".implode(',',$alist).")";
		$langs->load("poa@poa");

		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT c.rowid, c.label as label, c.partida AS partida, c.amount AS amount, ";
		$sql.= " s.sigla AS code_iso ";
		$sql.= " FROM ".MAIN_DB_PREFIX."poa_poa AS c ";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_structure AS s ON c.fk_structure = s.rowid ";

		$sql.= " WHERE c.entity = ".$conf->entity;
		$sql.= " AND c.statut = 1 ";
		$sql.= " AND s.gestion = ".$_SESSION['period_year'];
		$sql.= " AND s.pos = 3";
		if ($filter)
			$sql.= $filter;
	// if ($fk_structure)
	//   $sql.= " AND c.fk_structure = ".$fk_structure;
		$sql.= " ORDER BY s.sigla ASC";
		dol_syslog(get_class($this)."::select_poa sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$out.= '<select id="select'.$htmlname.'" class="form-control" name="'.$htmlname.'" '.$htmloption.'>';
			if ($showempty)
			{
				$out.= '<option value="-1"';
				if ($selected == -1) $out.= ' selected="selected"';
				$out.= '>&nbsp;</option>';
			}

			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				$foundselected=false;

				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$countryArray[$i]['rowid'] 	  = $obj->rowid;
					$countryArray[$i]['partida']  = $obj->partida;
					$countryArray[$i]['amount']  = $obj->amount;
					$countryArray[$i]['code_iso'] = $obj->code_iso;
					$countryArray[$i]['label']	  = ($obj->code_iso && $langs->transnoentitiesnoconv("Poa".$obj->code_iso)!="Poa".$obj->code_iso?$langs->transnoentitiesnoconv("Poa".$obj->code_iso):($obj->label!='-'?$obj->label:''));
					$label[$i] 	= $countryArray[$i]['label'];
					$i++;
				}

				array_multisort($label, SORT_ASC, $countryArray);

				foreach ($countryArray as $row)
				{
					//print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
					if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['code_iso'] || $selected == $row['label']) )
					{
						$foundselected=true;
						$out.= '<option value="'.$row['rowid'].'" selected="selected">';
					}
					else
					{
						$out.= '<option value="'.$row['rowid'].'">';
					}
					$out.= dol_trunc($row['label'],$maxlength,'middle');
					if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')'.' '.$row['partida'].' '.price($row['amount']);
					$out.= '</option>';
				}
			}
			$out.= '</select>';
		}
		else
		{
			dol_print_error($this->db);
		}

		return $out;
	}


	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function get_partida($fk_structure,$period_year)
	{
		global $langs;
		if (empty($period_year)) $period_year = date('Y');
		$sql = "SELECT";
		$sql.= " t.partida,";
		$sql.= " t.partida";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as t";
		$sql.= " WHERE t.fk_structure = ".$fk_structure;
		$sql.= " AND t.gestion = ".$period_year;
		$sql.= " GROUP BY t.partida ";
		$sql.= " ORDER BY t.partida ";
		dol_syslog(get_class($this)."::get_partida sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				$this->array = array();
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$this->array[$obj->partida] = $obj->partida;
					$i++;
				}
			}
			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::get_partida ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function search($search,$period_year=0)
	{
		global $langs,$conf;
		if (empty($period_year)) $period_year = date('Y');
		$sql = "SELECT t.rowid, ";
		$sql.= " t.label, ";
		$sql.= " t.partida, ";
		$sql.= " s.sigla ";
		$sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_structure as s ON t.fk_structure = s.rowid ";
		$sql.= " WHERE t.entity = ".$conf->entity;
		$sql.= " AND t.gestion = ".$period_year;
		$sql .= " AND ( t.label LIKE '%".$this->db->escape($search)."%'";
		$sql .= " OR t.pseudonym LIKE '%".$this->db->escape($search)."%'";
		$sql .= " OR s.sigla LIKE '%".$this->db->escape($search)."%' )";
		$sql.= " ORDER BY s.sigla, t.label ";
		dol_syslog(get_class($this)."::search sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($num)
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$this->array[$obj->rowid] = $obj->rowid;
					$i++;
				}
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::search ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$period_year    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function get_maxmin($period_year,$vari='M')
	{
		global $langs,$conf;
		if (empty($period_year)) $period_year = date('Y');
		$sql = "SELECT t.gestion, ";
		if ($vari == 'M')
			$sql.= " MAX(t.amount) as resultado ";
		if ($vari == 'm')
			$sql.= " MIN(t.amount) as resultado ";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as t";
		$sql.= " WHERE t.entity = ".$conf->entity;
		$sql.= " AND t.gestion = ".$period_year;
		$sql.= " AND t.statut = 1";
		$sql.= " GROUP BY t.gestion";
		dol_syslog(get_class($this)."::get_max sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);
				$this->maxmin = $obj->resultado;
				$i++;
			}
			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::get_max ".$this->error, LOG_ERR);
			return -1;
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$getion    object period_year
	 *  @param	int		$fk_user    Id object user
	 *  @param	varchar		$active    T=Todos, A=Activo, I=Inactivo

	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getlist_user($period_year,$fk_user=0,$fk_area=0,$active='')
	{
	  //
		global $langs,$conf;
		$sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.fk_structure,";
		$sql.= " t.ref,";
		$sql.= " t.sigla,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.partida,";
		$sql.= " t.amount,";
		$sql.= " t.classification,";
		$sql.= " t.source_verification,";
		$sql.= " t.unit,";
		$sql.= " t.responsible_one,";
		$sql.= " t.responsible_two,";
		$sql.= " t.responsible,";
		$sql.= " t.m_jan,";
		$sql.= " t.m_feb,";
		$sql.= " t.m_mar,";
		$sql.= " t.m_apr,";
		$sql.= " t.m_may,";
		$sql.= " t.m_jun,";
		$sql.= " t.m_jul,";
		$sql.= " t.m_aug,";
		$sql.= " t.m_sep,";
		$sql.= " t.m_oct,";
		$sql.= " t.m_nov,";
		$sql.= " t.m_dec,";
		$sql.= " t.p_jan,";
		$sql.= " t.p_feb,";
		$sql.= " t.p_mar,";
		$sql.= " t.p_apr,";
		$sql.= " t.p_may,";
		$sql.= " t.p_jun,";
		$sql.= " t.p_jul,";
		$sql.= " t.p_aug,";
		$sql.= " t.p_sep,";
		$sql.= " t.p_oct,";
		$sql.= " t.p_nov,";
		$sql.= " t.p_dec,";
		$sql.= " t.fk_area,";
		$sql.= " t.weighting,";
		$sql.= " t.fk_poa_reformulated,";
		$sql.= " t.version,";
		$sql .= " t.fk_user_create,";
		$sql .= " t.fk_user_mod,";
		$sql .= " t.datec,";
		$sql .= " t.datem,";
		$sql .= " t.tms,";
		$sql.= " t.statut,";
		$sql.= " t.statut_ref,";
		$sql.= " t.active,";
		$sql.= " u.fk_user";

		$sql.= " FROM ".MAIN_DB_PREFIX."poa_poa as t";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_structure AS s ON t.fk_structure = s.rowid";

		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."poa_poa_user AS u ON t.rowid = u.fk_poa_poa";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."user AS us ON u.fk_user = us.rowid";
		$sql.= " WHERE t.gestion = ".$period_year;
		$sql.= " AND t.entity = ".$conf->entity;
		$sql.= " AND u.active = 1 ";
		$sql.= " AND u.statut = 1";
		$sql.= " AND t.statut = 1";
		$sql.= " AND t.statut_ref = 1";
		if ($fk_user > 0)
			$sql.= " AND u.fk_user = ".$fk_user;
		if ($fk_area > 0)
			$sql.= " AND s.fk_area = ".$fk_area;
		if ($active == 'A') $sql.= " AND t.active = 1";
		if ($active == 'I') $sql.= " AND t.active = 0";
		$sql.= " ORDER BY us.lastname, us.firstname";
		dol_syslog(get_class($this)."::getlist_user sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($this->db->num_rows($resql))
			{
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$objnew = new Poapoa($this->db);

					$objnew->id    = $obj->rowid;

					$objnew->entity = $obj->entity;
					$objnew->gestion = $obj->gestion;
					$objnew->fk_structure = $obj->fk_structure;
					$objnew->ref = $obj->ref;
					$objnew->sigla = $obj->sigla;
					$objnew->label = $obj->label;
					$objnew->pseudonym = $obj->pseudonym;
					$objnew->partida = $obj->partida;
					$objnew->amount = $obj->amount;
					$objnew->classification = $obj->classification;
					$objnew->source_verification = $obj->source_verification;
					$objnew->unit = $obj->unit;
					$objnew->responsible_one = $obj->responsible_one;
					$objnew->responsible_two = $obj->responsible_two;
					$objnew->responsible = $obj->responsible;
					$objnew->m_jan = $obj->m_jan;
					$objnew->m_feb = $obj->m_feb;
					$objnew->m_mar = $obj->m_mar;
					$objnew->m_apr = $obj->m_apr;
					$objnew->m_may = $obj->m_may;
					$objnew->m_jun = $obj->m_jun;
					$objnew->m_jul = $obj->m_jul;
					$objnew->m_aug = $obj->m_aug;
					$objnew->m_sep = $obj->m_sep;
					$objnew->m_oct = $obj->m_oct;
					$objnew->m_nov = $obj->m_nov;
					$objnew->m_dec = $obj->m_dec;
					$objnew->p_jan = $obj->p_jan;
					$objnew->p_feb = $obj->p_feb;
					$objnew->p_mar = $obj->p_mar;
					$objnew->p_apr = $obj->p_apr;
					$objnew->p_may = $obj->p_may;
					$objnew->p_jun = $obj->p_jun;
					$objnew->p_jul = $obj->p_jul;
					$objnew->p_aug = $obj->p_aug;
					$objnew->p_sep = $obj->p_sep;
					$objnew->p_oct = $obj->p_oct;
					$objnew->p_nov = $obj->p_nov;
					$objnew->p_dec = $obj->p_dec;
					$objnew->fk_area = $obj->fk_area;
					$objnew->weighting = $obj->weighting;
					$objnew->fk_poa_reformulated = $obj->fk_poa_reformulated;
					$objnew->version = $obj->version;
					$objNew->fk_user_create = $obj->fk_user_create;
					$objNew->fk_user_mod = $obj->fk_user_mod;
					$objNew->datec = $this->db->jdate($obj->datec);
					$objNew->datem = $this->db->jdate($obj->datem);
					$objNew->tms = $this->db->jdate($obj->tms);
					$objnew->statut = $obj->statut;
					$objnew->statut_ref = $obj->statut_ref;
					$objnew->active = $obj->active;
					if ($fk_user == '')
						$this->array[$obj->fk_user][$obj->rowid] = $objnew;
					else
						$this->array[$fk_user][$obj->rowid] = $objnew;
					$i++;
				}
			}
			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::getlist_user ".$this->error, LOG_ERR);
			return -1;
		}
	}
}
?>