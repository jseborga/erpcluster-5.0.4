<?php
require_once DOL_DOCUMENT_ROOT.'/poa/class/poastructure.class.php';

class Poastructureext extends Poastructure
{
	//MODIFICADO

	var $max='';

	public function get_structurenext($period_year)
	{
		global $conf;

		$sql = 'SELECT';
		$sql .= " MAX(t.ref) AS max";
		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		$sql.= ' WHERE entity = '.$conf->entity;
		if ($this->id >0)	
			$sql.= " AND t.fk_father = ".$this->id;
		else
			$sql.= " AND t.gestion = ".$period_year;
		//$sql.= " GROUP BY t.fk_father";

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->max = $obj->max;
			}

			$this->db->free($resql);

			if ($numrows) {
				return $numrows;
			} else {
				return 0;
			}
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . implode(',', $this->errors), LOG_ERR);
			return - 1;
		}
	}
	
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
		$sql.= " t.type,";
		$sql.= " t.fk_father,";
		$sql.= " t.fk_area,";
		$sql.= " t.sigla,";
		$sql.= " t.ref,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.unit,";
		$sql.= " t.pos,";
		$sql.= " t.version,";
		$sql.= " t.statut";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_structure as t";
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
					$objNew = new Poastructure($this->db);
					$objNew->id    = $obj->rowid;

					$objNew->entity = $obj->entity;
					$objNew->gestion = $obj->gestion;
					$objNew->type = $obj->type;
					$objNew->fk_father = $obj->fk_father;
					$objNew->fk_area = $obj->fk_area;
					$objNew->ref = $obj->ref;
					$objNew->sigla = $obj->sigla;
					$objNew->label = $obj->label;
					$objNew->pseudonym = $obj->pseudonym;
					$objNew->unit = $obj->unit;
					$objNew->pos = $obj->pos;
					$objNew->version = $obj->version;
					$objNew->statut = $obj->statut;

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
	 *  @param  int     $id    Id object
	 *  @return int             <0 if KO, >0 if OK
	 */
	function getlist_area($fk_area,$period_year)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.type,";
		$sql.= " t.fk_father,";
		$sql.= " t.fk_area,";
		$sql.= " t.sigla,";
		$sql.= " t.ref,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.unit,";
		$sql.= " t.pos,";
		$sql.= " t.version,";
		$sql.= " t.statut";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_structure as t";
		$sql.= " WHERE t.fk_area = ".$fk_area;
		$sql.= " AND t.gestion = ".$period_year;
		dol_syslog(get_class($this)."::getlist_area sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->array = array();
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
					$objNew = new Poastructure($this->db);
					$objNew->id    = $obj->rowid;

					$objNew->entity = $obj->entity;
					$objNew->gestion = $obj->gestion;
					$objNew->type = $obj->type;
					$objNew->fk_father = $obj->fk_father;
					$objNew->fk_area = $obj->fk_area;
					$objNew->ref = $obj->ref;
					$objNew->sigla = $obj->sigla;
					$objNew->label = $obj->label;
					$objNew->pseudonym = $obj->pseudonym;
					$objNew->unit = $obj->unit;
					$objNew->pos = $obj->pos;
					$objNew->version = $obj->version;
					$objNew->statut = $obj->statut;

					$this->array[$obj->rowid] = $objNew;
					$i++;
				}
				$this->db->free($resql);
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
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param	string	$selected       Id or Code or Label of preselected country
	 *  @param  string	$htmlname       Name of html select object
	 *  @param  string	$htmloption     Options html on select object
	 *  @param	string	$maxlength	Max length for labels (0=no limit)
	 *  @param	string	$showempty	View space labels (0=no view)

	 *  @return string           		HTML string with select
	 */
	function select_structure($selected='',$htmlname='fk_father',$htmloption='',$maxlength=0,$showempty=0,$pos=3)
	{
		global $conf,$langs;

		$langs->load("poa@poa");

		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT c.rowid, c.sigla as label, c.label as code_iso, c.fk_father";
		$sql.= " FROM ".MAIN_DB_PREFIX."poa_structure AS c ";
		$sql.= " WHERE c.entity = ".$conf->entity;
		$sql.= " AND c.gestion = ".$_SESSION['period_year'];
		if (!empty($pos))
			$sql.= " AND c.pos = ".$pos;
		$sql.= " ORDER BY c.sigla ASC";
		dol_syslog(get_class($this)."::select_structure sql=".$sql);
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
					$countryArray[$i]['rowid'] 		= $obj->rowid;
					$countryArray[$i]['code_iso'] 	= $obj->code_iso;

					$countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Structure".$obj->code_iso)!="Structure".$obj->code_iso?$langs->transnoentitiesnoconv("Structure".$obj->code_iso):($obj->label!='-'?$obj->label:''));
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
					//$out.= dol_trunc($row['label'],$maxlength,'middle');
					$out.= $row['label'];
					if ($row['code_iso']) $out.= ' ('.dol_trunc($row['code_iso'],$maxlength,'middle') . ')';
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

	//MODIFICADO
	/**
	 *	Return label of status of object
	 *
	 *	@param      int	$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
	 *	@param      int	$type       0=Shell, 1=Buy
	 *	@return     string      	Label of status
	 */
	function getLibStatutx($mode=0, $type=0)
	{
		if($type==0)
			return $this->LibStatut($this->statut,$mode,$type);
		else
			return $this->LibStatut($this->statut_ref,$mode,$type);
	}

	/**
	 *	Return label of a given status
	 *
	 *	@param      int		$status     Statut
	 *	@param      int		$mode       0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto
	 *	@param      int		$type       0=Status "to sell", 1=Status "to buy"
	 *	@return     string      		Label of status
	 */
	function LibStatutx($status,$mode=0,$type=0)
	{
		global $langs;
		$langs->load('poa@poa');

		if ($mode == 0)
		{
			if ($status == 0) return img_picto($langs->trans('Notapproved'),'statut5').' '.($type==0 ? $langs->trans('Notapproved'):$langs->trans('Reformulation unapproved'));
			if ($status == 1) return img_picto($langs->trans('Approved'),'statut4').' '.($type==0 ? $langs->trans('Approved'):$langs->trans('Reformulation approved'));
		}

		if ($mode == 2)
		{
			if ($status == 0) return img_picto($langs->trans('Notapproved'),'statut5').' '.($type==0 ? $langs->trans('Notapproved'):$langs->trans('Reformulation unapproved'));
			if ($status == 1) return img_picto($langs->trans('Approved'),'statut4').' '.($type==0 ? $langs->trans('Approved'):$langs->trans('Reformulation approved'));
		}

		return $langs->trans('Unknown');
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getstructure($fk_structure)
	{
		global $langs;
		$lRet = true;
		$fk_str = $fk_structure;
		$this->aList = array();
		while ($lRet == true)
		{
			$this->fetch($fk_str);
			if ($this->id == $fk_str)
			{
				if ($this->fk_father > 0)
				{
					$this->aList[$fk_str][$this->fk_father] = $fk_str;
					$fk_str = $this->fk_father;
				}
				else
				{
					$this->aList[$fk_str][$this->fk_father] = $fk_str;
					$lRet = false;
				}
			}
			else
			{
				$this->error="Error ".$this->db->lasterror();
				dol_syslog(get_class($this)."::getstructure ".$this->error, LOG_ERR);
				$lRet = false;
				return -1;
			}
		}
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 * date 06/01/2015
	 */
	function fetch_sigla($sigla,$period_year)
	{
		global $langs,$conf;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.type,";
		$sql.= " t.fk_father,";
		$sql.= " t.fk_area,";
		$sql.= " t.ref,";
		$sql.= " t.sigla,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.unit,";
		$sql.= " t.pos,";
		$sql.= " t.version,";
		$sql.= " t.statut";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_structure as t";
		$sql.= " WHERE t.sigla = '".$sigla."'";
		$sql.= " AND t.gestion = ".$period_year;
		$sql.= " AND t.entity = ".$conf->entity;
		dol_syslog(get_class($this)."::fetch_sigla sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;

				$this->entity = $obj->entity;
				$this->gestion = $obj->gestion;
				$this->type = $obj->type;
				$this->fk_father = $obj->fk_father;
				$this->fk_area = $obj->fk_area;
				$this->ref = $obj->ref;
				$this->sigla = $obj->sigla;
				$this->label = $obj->label;
				$this->pseudonym = $obj->pseudonym;
				$this->unit = $obj->unit;
				$this->pos = $obj->pos;
				$this->version = $obj->version;
				$this->statut = $obj->statut;


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
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function getliststr($period_year)
	{
		global $langs;
		$lRet = true;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.gestion,";
		$sql.= " t.type,";
		$sql.= " t.fk_father,";
		$sql.= " t.fk_area,";
		$sql.= " t.ref,";
		$sql.= " t.sigla,";
		$sql.= " t.label,";
		$sql.= " t.pseudonym,";
		$sql.= " t.unit,";
		$sql.= " t.pos,";
		$sql.= " t.version,";
		$sql.= " t.statut";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_structure as t";
		$sql.= " WHERE t.gestion = ".$period_year;
	  //$sql.= " AND t.statut = 1";

		dol_syslog(get_class($this)."::getliststr sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->aList = array();
		$array = array();
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			if ($this->db->num_rows($resql))
			{
				$j = 0;
				while ($j < $num)
				{
					$array = array();
					$obj = $this->db->fetch_object($resql);
					$lRet = true;
			  //		      $this->aList[$obj->fk_father][$obj->rowid] = $obj->rowid;
					$this->aList[$obj->rowid] = array('father' =>$obj->fk_father,
						'pos' => $obj->pos,
						'obj' => $obj);
					$j++;
				}
				return 1;
			}
			return 0;
		}
		return -1;
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function fetch_son($fk_father)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

	  // $sql.= " t.entity,";
	  // $sql.= " t.gestion,";
		$sql.= " t.fk_father";
	  // $sql.= " t.fk_area,";
	  // $sql.= " t.ref,";
	  // $sql.= " t.sigla,";
	  // $sql.= " t.label,";
	  // $sql.= " t.pseudonym,";
	  // $sql.= " t.pos,";
	  // $sql.= " t.version,";
	  // $sql.= " t.statut";


		$sql.= " FROM ".MAIN_DB_PREFIX."poa_structure as t";
		$sql.= " WHERE t.fk_father = ".$fk_father;
		echo '<hr>'.$sql.= " ORDER BY t.rowid";
		dol_syslog(get_class($this)."::fetch_son sql=".$sql, LOG_DEBUG);
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
			  // $objnew = new Poastructure($this->db);

			  // $objnew->id    = $obj->rowid;

			  // $objnew->entity = $obj->entity;
			  // $objnew->period_year = $obj->period_year;
			  // $objnew->fk_father = $obj->fk_father;
			  // $objnew->fk_area = $obj->fk_area;
			  // $objnew->ref = $obj->ref;
			  // $objnew->sigla = $obj->sigla;
			  // $objnew->label = $obj->label;
			  // $objnew->pseudonym = $obj->pseudonym;
			  // $objnew->pos = $obj->pos;
			  // $objnew->version = $obj->version;
			  // $objnew->statut = $obj->statut;
					$this->array[$obj->rowid] = $obj->fk_father;
					$i++;
				}
				$this->db->free($resql);
				return 1;
			}
			$this->db->free($resql);
			return 0;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_son ".$this->error, LOG_ERR);
			return -1;
		}
	}

	function get_search($rowid,$id,$aArray)
	{
		$res1 = True;
		while ($res1 == True)
		{
			$this->array = array();
			$this->fetch_son($id);
			if (count($this->array)>0)
			{
				foreach ((array) $this->array AS $k => $fk_father)
				{
					$aArray[$fk_father][$k] = $k;
					$resx = $this->get_search($rowid,$k,$aArray);
				}
				$res1 = 0;
			}
			else
				$res1 = 0;
		}
		$this->aList[$rowid] = $aArray;
		return $res1;
		  //		}
	}

	//function lines pl
	function getlinespl()
	{
		global $langs;
		include_once DOL_DOCUMENT_ROOT.'/poa/structure/class/poastructurepl.class.php';
		$objstrpl = new Poastructurepl($this->db);
		$objstrpl->getlist_yearmonth($this->id);
		return $objstrpl->array;
	}
	//function lines ej
	function getlinesej()
	{
		global $langs;
		include_once DOL_DOCUMENT_ROOT.'/poa/structure/class/poastructureej.class.php';
		$objstrej = new Poastructureej($this->db);
		$objstrej->getlist_yearmonth($this->id);
		return $objstrej->array;
	}
	
}
?>
