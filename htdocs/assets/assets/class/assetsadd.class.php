<?php
require_once DOL_DOCUMENT_ROOT.'/assets/assets/class/assets.class.php';

class Assetsadd extends Assets
{
		//MODIFICADO
	/**
	 *  Load object in memory from the database
	 *
	 *  @param	int		$id    Id object
	 *  @return int          	<0 if KO, >0 if OK
	 */
	function fetch_max($type)
	{
		global $langs,$conf;
		$sql = "SELECT";
		$sql.= " MAX(t.item_asset) AS item_asset";

		$sql.= " FROM ".MAIN_DB_PREFIX."assets as t";
		$sql.= " WHERE t.entity = ".$conf->entity;
		$sql.= " AND t.type_group = '".$type."'";

		dol_syslog(get_class($this)."::fetch_max sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		$this->maximo = 1;
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);
				$this->maximo = $obj->item_asset + 1;
			}
			$this->db->free($resql);
			return 1;
		}
		else
		{
			$this->error="Error ".$this->db->lasterror();
			dol_syslog(get_class($this)."::fetch_max ".$this->error, LOG_ERR);
			return -1;
		}
	}

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

		$dir = DOL_DOCUMENT_ROOT . "/assets/core/modules";

		if (! empty($conf->global->ASSETS_ADDON))
		{
			$file = $conf->global->ASSETS_ADDON.".php";
			// Chargement de la classe de numerotation
			$classname = $conf->global->ASSETS_ADDON;
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
					dol_print_error($db,"Assets::getNextNumRef ".$obj->error);
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
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param	string	$selected       Id or Code or Label of preselected country
	 *  @param  string	$htmlname       Name of html select object
	 *  @param  string	$htmloption     Options html on select object
	 *  @param	string	$maxlength		Max length for labels (0=no limit)
	 *  @return string           		HTML string with select
	 */
	function select_assets($selected='',$htmlname='fk_asset',$htmloption='',$maxlength=0,$showempty=0,$idnot=0,$required='',$exclude='',$include='',$mark='')
	{
		global $conf,$langs;

		$langs->load("mant@mant");
		if ($required)
			$required = 'required="required"';
		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT c.rowid, c.ref as code_iso, c.descrip as label";
		$sql.= " FROM ".MAIN_DB_PREFIX."assets AS c ";
		$sql.= " WHERE c.entity = ".$conf->entity;
		$sql.= " AND c.statut = 1";
		if ($idnot) $sql.= " AND c.rowid NOT IN (".$idnot.")";
		//if ($mark) $sql.= " AND (c.mark iS NULL OR c.mark = '' OR c.mark = ' ')";
		$sql.= " ORDER BY c.ref ASC";
		//echo $sql;
		dol_syslog(get_class($this)."::select_assets sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$out.= '<select id="select'.$htmlname.'" class="flat selectpays" '.$required.' name="'.$htmlname.'" '.$htmloption.'>';
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
					if (empty($exclude[$obj->rowid]))
					{
						if (empty($include))
						{
							$countryArray[$i]['rowid'] 		= $obj->rowid;
							$countryArray[$i]['code_iso'] 	= $obj->code_iso;
							$countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Area".$obj->code_iso)!="Area".$obj->code_iso?$langs->transnoentitiesnoconv("Area".$obj->code_iso):($obj->label!='-'?$obj->label:''));
							$label[$i] 	= $countryArray[$i]['label'];
						}
						elseif($include[$obj->rowid])
						{
							$countryArray[$i]['rowid'] 		= $obj->rowid;
							$countryArray[$i]['code_iso'] 	= $obj->code_iso;
							$countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv("Area".$obj->code_iso)!="Area".$obj->code_iso?$langs->transnoentitiesnoconv("Area".$obj->code_iso):($obj->label!='-'?$obj->label:''));
							$label[$i] 	= $countryArray[$i]['label'];
						}
					}
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