<?php
require_once DOL_DOCUMENT_ROOT.'/almacen/local/class/entrepotrelation.class.php';

class Entrepotrelationext extends Entrepotrelation
{
	/**
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param	string	$selected       Id or Code or Label of preselected country
	 *  @param  string	$htmlname       Name of html select object
	 *  @param  string	$htmloption     Options html on select object
	 *  @return string           		HTML string with select
	 */
	function select_padre($selected='',$htmlname='fk_entrepot',$htmloption='',$excluded='',$included='',$viewempty=1)
	{
		global $conf,$langs;

		$langs->load("dict");

		$out='';
		$padreArray=array();
		$label=array();

		$sql = "SELECT rowid, description, label, lieu AS code_iso";
		$sql.= " FROM ".MAIN_DB_PREFIX."entrepot AS e ";
		$sql.= " WHERE statut = 1";
		if ($excluded)
			$sql.= " AND e.rowid NOT IN (".$excluded.")";
		if ($included)
			$sql.= " AND e.rowid IN (".$included.")";
		$sql.= " ORDER BY description ASC";

		dol_syslog(get_class($this)."::select_padre sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
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
					$countryArray[$i]['lieu']   	= $obj->lieu;
					$countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Country".$obj->code_iso)!="Country".$obj->code_iso?$langs->transnoentitiesnoconv("Country".$obj->code_iso):($obj->label!='-'?$obj->label:''));
					$label[$i] 	= $countryArray[$i]['label'];
					$i++;
				}

				array_multisort($label, SORT_ASC, $countryArray);
				if ($viewempty)
					$out.='<option value="-1"'.($id==-1?' selected="selected"':'').'>&nbsp;</option>'."\n";

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
					$out.= $row['label'];
					if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
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
	 *	Load all detailed lines into this->lines
	 *
	 *	@return     int         1 if OK, < 0 if KO
	 */
	function fetch_lines()
	{
		global $conf;
		$this->lines=array();
		require_once DOL_DOCUMENT_ROOT.'/almacen/class/solalmacendet.class.php';
		$aRowid = array();
	   //revisando si el local es padre de otros almacenes
	   //si es afirmativo se debe sumar todas las cantidades de sus hijos incluso del padre
		$sql  = "SELECT er.rowid,er.rowid AS id ";
		$sql.= " FROM ".MAIN_DB_PREFIX."entrepot_relation AS er";
		$sql.= " WHERE er.fk_entrepot_father = ".$this->id;
		$sql.= " GROUP BY er.rowid ";

		$result = $this->db->query($sql);
		$ids = '';
		$filtroEntrepot = " sm.fk_entrepot = ".$this->id;
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;
			$aRowid[$this->id] = $this->id;
			while ($i < min($num,100))
			{
				$objp = $this->db->fetch_object($result);
				$aRowid[$objp->rowid] = $objp->rowid;
				$i++;
			}
			$ids = implode(',',$aRowid);
			$filtroEntrepot = " sm.fk_entrepot IN(".$ids.")";
		}
		//movimiento del producto
		$sql  = "SELECT p.rowid, p.ref, p.label, SUM(sm.value) AS saldo, SUM(sm.value*sm.price) AS total ";
		$sql.= " FROM ".MAIN_DB_PREFIX."stock_mouvement AS sm";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."product AS p";
		$sql.= " ON sm.fk_product = p.rowid ";
		$sql.= " WHERE ";
		$sql.= $filtroEntrepot;
		$sql.= " AND p.entity = ".$conf->entity;
		$sql.= " GROUP BY p.rowid, p.ref ";
		$sql.= " ORDER BY p.ref ";
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;
			if ($num)
			{
				$var=True;
				while ($i < $num)
				{
					  //actualizando totales
					$objp = $this->db->fetch_object($result);
					$line = new Solalmacendet($this->db);

					$line->rowid  = $objp->rowid;
					$line->ref    = $objp->ref;
					$line->label  = $objp->label;
					$line->saldo  = $objp->saldo;
					$line->total  = $objp->total;

					$this->lines[$i] = $line;
					$this->linesprod[$objp->rowid] = $line;
					$i++;
				}
				$this->db->free($result);
			}
			return 1;
		}
		else
		{
			$this->error=$this->db->error();
			dol_syslog(get_class($this).'::fetch_lines '.$this->error,LOG_ERR);
			return -3;
		}
	}

	/**
	 *	Load all detailed lines into this->lines
	 *
	 *	@return     int         1 if OK, < 0 if KO
	 */
	function fetch_entrepot()
	{

		//revisando si el local es padre de otros almacenes
		//si es afirmativo se debe sumar todas las cantidades de sus hijos incluso del padre
		$sql  = "SELECT er.rowid,er.rowid AS id ";
		$sql.= " FROM ".MAIN_DB_PREFIX."entrepot_relation AS er";
		$sql.= " WHERE er.fk_entrepot_father = ".$this->id;
		$sql.= " GROUP BY er.rowid ";

		$result = $this->db->query($sql);
		$ids = '';
		$this->filtroEntrepot = " sm.fk_entrepot = ".$this->id;
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;
			$aRowid[$this->id] = $this->id;
			while ($i < min($num,100))
			{
				$objp = $this->db->fetch_object($result);
				$aRowid[$objp->rowid] = $objp->rowid;
				$i++;
			}
			$this->aArray = $aRowid;
			return 1;
		}
		else
		{
			$this->error=$this->db->error();
			dol_syslog(get_class($this).'::fetch_entrepot '.$this->error,LOG_ERR);
			return -3;
		}
	}


}

?>