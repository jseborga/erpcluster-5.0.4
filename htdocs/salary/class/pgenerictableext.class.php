<?php
require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenerictable.class.php';

class Pgenerictableext extends Pgenerictable
{
	var $aTable;
	 /**
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param  string  $selected       Id or Code or Label of preselected country
	 *  @param  string  $htmlname       Name of html select object
	 *  @param  string  $htmloption     Options html on select object
	 *  @param  string  $maxlength      Max length for labels (0=no limit)
	 *  @return string                  HTML string with select
	 */
	 function select_generic_table($selected='',$htmlname='fk_generic_table',$htmloption='',$maxlength=0,$showempty=0)
	 {
	 	global $conf,$langs;

	 	$langs->load("salary@salary");

	 	$out='';
	 	$countryArray=array();
	 	$label=array();

	 	$sql = "SELECT table_cod as code_iso, table_name AS label";
	 	$sql.= " FROM ".MAIN_DB_PREFIX."p_generic_table";
	 	$sql.= " WHERE entity = ".$conf->entity;
	 	$sql.= " GROUP BY table_cod, table_name ";
	 	$sql.= " ORDER BY table_name ASC";

	 	dol_syslog(get_class($this)."::select_generic_table sql=".$sql);
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
	 				$countryArray[$i]['code_iso']   = $obj->code_iso;
	 				$countryArray[$i]['label']      = $obj->label;
	 				$label[$i]  = $countryArray[$i]['label'];
	 				$i++;
	 			}

	 			array_multisort($label, SORT_ASC, $countryArray);

	 			foreach ($countryArray as $row)
	 			{
					//print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
	 				if ($selected && $selected != '-1' && ($selected == $row['code_iso'] || $selected == $row['label']) )
	 				{
	 					$foundselected=true;
	 					$out.= '<option value="'.$row['code_iso'].'" selected="selected">';
	 				}
	 				else
	 				{
	 					$out.= '<option value="'.$row['code_iso'].'">';
	 				}
	 				$out.= dol_trunc($row['label'],$maxlength,'middle');
					//if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
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
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param  string  $selected       Id or Code or Label of preselected
	 *  @param  string  $htmlname       Name of html select object
	 *  @param  string  $htmloption     Options html on select object
	 *  @param  string  $maxlength  Max length for labels (0=no limit)
	 *  @param  string  $showempty  add empty register 0=not, 1=yes

	 *  @return string                  HTML string with select
	 */
	function select_generic_table_field($selected='',$htmlname='fk_generic_table',$htmloption='',$maxlength=0,$showempty=0)
	{
		global $conf,$langs;

		$langs->load("salary@salary");

		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT rowid as code_iso, table_name AS label, field_name AS fieldlabel";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_generic_table";
		$sql.= " WHERE entity = ".$conf->entity;
		$sql.= " ORDER BY table_name ASC, field_name ASC";

		dol_syslog(get_class($this)."::select_generic_table_field sql=".$sql);
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
					$countryArray[$i]['code_iso']   = $obj->code_iso;
					$countryArray[$i]['label']      = $obj->label.' - '.$obj->fieldlabel;
					$label[$i]  = $countryArray[$i]['label'];
					$i++;
				}

				array_multisort($label, SORT_ASC, $countryArray);
				foreach ($countryArray as $row)
				{
					//print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
					if ($selected && $selected != '-1' && ($selected == $row['code_iso'] || $selected == $row['label']) )
					{
						$foundselected=true;
						$out.= '<option value="'.$row['code_iso'].'" selected="selected">';
					}
					else
					{
						$out.= '<option value="'.$row['code_iso'].'">';
					}
					$out.= dol_trunc($row['label'],$maxlength,'middle');
					//if ($row['code_iso']) $out.= ' ('.$row['code_iso'] . ')';
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
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param  string  $selected       Id or Code or Label of preselected country
	 *  @param  string  $htmlname       Name of html select object
	 *  @param  string  $htmloption     Options html on select object
	 *  @param  string  $maxlength      Max length for labels (0=no limit)
	 *  @return string                  HTML string with select
	 */
	function array_table($table_cod)
	{
		global $conf,$langs;

		$langs->load("salary@salary");

		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT rowid, ref, field_name ";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_generic_table";
		$sql.= " WHERE entity = ".$conf->entity;
		$sql.= " AND table_cod = '".$table_cod."' ";
		$sql.= " ORDER BY sequen ASC";

		dol_syslog(get_class($this)."::array_table sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$aArray = array();

			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);
					$aArray[$obj->rowid] = $obj->field_name;
					$i++;
				}
			}
		}
		else
		{
			dol_print_error($this->db);
		}

		return $aArray;
	}

	/**
	 *  Load object in memory from the database
	 *
	 *  @param  varchar     $table_cod     object
	 *  @param  int     $sequen    sequen object
	 *  @return int             <0 if KO, >0 if OK
	 */
	function fetch_table_cod($table_cod,$sequen)
	{
		global $langs,$conf;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.entity,";
		$sql.= " t.table_cod,";
		$sql.= " t.table_name,";
		$sql.= " t.field_name,";
		$sql.= " t.sequen,";
		$sql.= " t.limits,";
		$sql.= " t.type_value,";
		$sql.= " t.state";


		$sql.= " FROM ".MAIN_DB_PREFIX."p_generic_table as t";
		$sql.= " WHERE t.entity = ".$conf->entity;
		$sql.= " AND t.table_cod = '".$table_cod."' ";
		$sql.= " AND t.sequen = ".$sequen;

		dol_syslog(get_class($this)."::fetch_table_cod sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;

				$this->entity = $obj->entity;
				$this->table_cod = $obj->table_cod;
				$this->table_name = $obj->table_name;
				$this->field_name = $obj->field_name;
				$this->sequen = $obj->sequen;
				$this->limits = $obj->limits;
				$this->type_value = $obj->type_value;
				$this->state = $obj->state;
			}
			$this->db->free($resql);

			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_table_cod ".$this->error, LOG_ERR);
			return -1;
		}
	}
	public function getTable($filter)
	{
		global $conf,$langs;
		require_once DOL_DOCUMENT_ROOT.'/salary/class/pgenericfieldext.class.php';
		$obj = new Pgenericfieldext($this->db);
		$num = $this->fetchAll('','',0,0,array(1=>1),'AND',$filter);
		$this->aTable = array();
		if ($num > 0)
		{
			$lines = $this->lines;
			foreach ($lines AS $j => $line)
			{
				$filterf = " AND t.generic_table_ref = '".$line->ref."'";
				$resf = $obj->fetchAll('ASC','t.sequen',0,0,array(1=>1),'AND',$filterf);
				if ($resf>0)
				{
					foreach ($obj->lines AS $k => $linef)
					{
						$this->aTable[$linef->sequen][$line->type_value] = $linef->field_value;
					}
				}
			}
		}
		return $num;
	}
}
?>