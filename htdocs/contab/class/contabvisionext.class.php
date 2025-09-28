<?php
require_once DOL_DOCUMENT_ROOT.'/contab/class/contabvision.class.php';

class Contabvisionext extends Contabvision
{
	function line_ult($fk_parent)
	{
		global $conf;
		$sql = "SELECT MAX(t.line) AS line ";
		$sql.= " FROM ".MAIN_DB_PREFIX."contab_vision as t";
		$sql.= " WHERE t.fk_parent = '".$fk_parent."'";
		$sql.= " AND t.entity = ".$conf->entity;

		$result=$this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			if ($this->db->num_rows($result))
			{
				$obj = $this->db->fetch_object($result);
				if(is_null($obj->line) && $fk_parent>0)
					$this->line = 2;
				else
					$this->line = $obj->line+1;
			}
			else
			{
				if ($fk_parent>0)
					$this->line = 2;
				else
					$this->line = 1;
			}
			$this->db->free($resql);
			return $num;
		}
		return -1;
	}


	function sequence_ult($ref)
	{
		global $conf;
		$sql = "SELECT s.rowid, s.name_vision, s.sequence, s.account ";
		$sql.= " FROM ".MAIN_DB_PREFIX."contab_vision as s";
		$sql.= " WHERE s.ref = '".$ref."'";
		$sql.= " AND entity = ".$conf->entity;
		$sql.= " ORDER BY sequence DESC ";
		$result=$this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			if ($this->db->num_rows($result))
			{
				$obj = $this->db->fetch_object($result);
				$valor = $obj->name_vision;
				$sequence = $obj->sequence + 10;
				$account = $obj->account + 1;
			}
			else
			{
				$valor = '';
				$sequence= '';
				$account = '';
			}
		}
		else
		{
			$valor = '';
			$sequence= '';
			$account = '';
		}
		return array($valor,$sequence,$account);
	}
	/**
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param	string	$selected       Id or Code or Label of preselected country
	 *  @param  string	$htmlname       Name of html select object
	 *  @param  string	$htmloption     Options html on select object
	 *  @param	string	$maxlength		Max length for labels (0=no limit)
	 *  @return string           		HTML string with select
	 */
	function select_vision($selected='',$htmlname='fk_vision',$htmloption='',$maxlength=0,$showempty=0)
	{
		global $conf,$langs;
		$langs->load("contab@contab");

		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT rowid, ref as code_iso, name_vision as label";
		$sql.= " FROM ".MAIN_DB_PREFIX."contab_vision";
		$sql.= " WHERE entity = ".$conf->entity;
		$sql.= " AND  line = '001' ";
		$sql.= " AND  sequence = 1 ";
		$sql.= " ORDER BY ref ASC";

		dol_syslog(get_class($this)."::select_vision sql=".$sql);
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
					$countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Accounting".$obj->code_iso)!="Accounting".$obj->code_iso?$langs->transnoentitiesnoconv("Accounting".$obj->code_iso):($obj->label!='-'?$obj->label:''));
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

}
?>