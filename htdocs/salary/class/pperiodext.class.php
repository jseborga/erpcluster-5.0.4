<?php
require_once DOL_DOCUMENT_ROOT.'/salary/class/pperiod.class.php';

class Pperiodext extends Pperiod
{

	/**
   *  Create a document onto disk according to template module.
   *
   *  @param      string    $modele     Force model to use ('' to not force)
   *  @param    Translate $outputlangs  Object langs to use for output
   *  @param      int     $hidedetails    Hide details of lines
   *  @param      int     $hidedesc       Hide description
   *  @param      int     $hideref        Hide ref
   *  @return     int                 0 if KO, 1 if OK
   */
	public function generateDocument($modele, $outputlangs, $hidedetails=0, $hidedesc=0, $hideref=0)
	{
		global $conf,$user,$langs;

		$langs->load("salary");

		// Positionne le modele sur le nom du modele a utiliser
		if (! dol_strlen($modele))
		{
				$modele = 'boleta';
		}

		$modelpath = "salary/core/modules/doc/";

		return $this->commonGenerateDocument($modelpath, $modele, $outputlangs, $hidedetails, $hidedesc, $hideref);
	}

		/**
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param	string	$selected       Id or Code or Label of preselected country
	 *  @param  string	$htmlname       Name of html select object
	 *  @param  string	$htmloption     Options html on select object
	 *  @param	string	$maxlength		Max length for labels (0=no limit)
	 *  @param	string	$showempty		show line
	 *  @param	string	$cClose		0 = todos, 1 = no cerrados, 2= cerrados
	 *  @return string           		HTML string with select
	 */
		function select_period($selected='',$htmlname='fk_period',$htmloption='',$maxlength=0,$showempty=0,$cClose=0,$statusapp=false)
		{
			global $conf,$langs;
			$filtro = '';
			if (!empty($cClose))
			{
				if ($cClose == 1)
					$filtro = " AND (d.date_close IS NULL OR d.date_close = '' ) ";
				if ($cClose == 2)
					$filtro = " AND (d.date_close IS NOT NULL AND d.date_close != '' ) ";
			}
			if ($statusapp)
				$filtro.= " AND d.status_app = 0 ";
			$langs->load("salary@salary");

			$out='';
			$countryArray=array();
			$label=array();
			if (STRTOUPPER($conf->db->type) == 'PGSQL')
				$sql = "SELECT d.rowid, d.ref as code_iso, (d.mes || ' ' || d.anio) AS label, t.detail AS label_typefol ";
			else
				$sql = "SELECT d.rowid, d.ref as code_iso, CONCAT(d.mes,'-',d.anio,': ',e.label) as label, t.detail AS label_typefol";
			$sql.= " FROM ".MAIN_DB_PREFIX."p_period AS d ";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_proces AS e ON d.fk_proces = e.rowid AND d.entity = e.entity ";
			$sql.= " INNER JOIN ".MAIN_DB_PREFIX."p_type_fol AS t ON d.fk_type_fol = t.rowid AND d.entity = t.entity ";
			$sql.= " ";
			$sql.= " WHERE d.entity = ".$conf->entity;
			$sql.= $filtro;
			$sql.= " ORDER BY d.anio ASC, d.mes ASC";
			dol_syslog(get_class($this)."::select_period sql=".$sql);
			$resql=$this->db->query($sql);
			if ($resql)
			{
				$out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
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
						$countryArray[$i]['rowid'] 		= $obj->rowid;
						$countryArray[$i]['code_iso'] 	= $obj->code_iso;
						$countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Typefol".$obj->code_iso)!="Typefol".$obj->code_iso?$langs->transnoentitiesnoconv("Typefol".$obj->code_iso):($obj->label!='-'?$obj->label:''));
						$countryArray[$i]['label_typefol']		= $obj->label_typefol;

						$label[$i] 	= $countryArray[$i]['label'];
						$aRef[$i] 	= $countryArray[$i]['code_iso'];
						$i++;
					}

					array_multisort($aRef, SORT_DESC, $countryArray);

					foreach ($countryArray as $row)
					{
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
						if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
						if ($row['label_typefol']) $out.= ' - '.dol_trunc($row['label_typefol'],$maxlength,'middle');
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
	 *  @param	int		$month    number object
	 *  @param	int		$year    number object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function fetch_month_year($month,$year)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";
		$sql.= " t.entity,";
		$sql.= " t.fk_proces,";
		$sql.= " t.fk_type_fol,";
		$sql.= " t.ref,";
		$sql.= " t.mes,";
		$sql.= " t.anio,";
		$sql.= " t.date_ini,";
		$sql.= " t.date_fin,";
		$sql.= " t.date_pay,";
		$sql.= " t.date_court,";
		$sql.= " t.date_close,";
		$sql.= " t.state";


		$sql.= " FROM ".MAIN_DB_PREFIX."p_period as t";
		$sql.= " WHERE t.mes = ".$month;
		$sql.+ " AND t.anio = ".$year;
		$sql.= " AND t.state = 1";
		dol_syslog(get_class($this)."::fetch_month_year sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;
				$this->entity = $obj->entity;
				$this->fk_proces = $obj->fk_proces;
				$this->fk_type_fol = $obj->fk_type_fol;
				$this->ref = $obj->ref;
				$this->mes = $obj->mes;
				$this->anio = $obj->anio;
				$this->date_ini = $this->db->jdate($obj->date_ini);
				$this->date_fin = $this->db->jdate($obj->date_fin);
				$this->date_pay = $this->db->jdate($obj->date_pay);
				$this->date_court = $this->db->jdate($obj->date_court);
				$this->date_close = $this->db->jdate($obj->date_close);
				$this->state = $obj->state;

				return 1;
			}
			$this->db->free($resql);

			return 0;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_month_year ".$this->error, LOG_ERR);
			return -1;
		}
	}
}
?>