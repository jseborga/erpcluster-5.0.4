<?php
/* Copyright (C) 2014-2014 Ramiro Queso        <ramiro@ubuntu-bo.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/mant/lib/mant.lib.php
 *	\ingroup    Librerias
 *	\brief      Page fiche mantenimiento
 */

function select_tables($selected='',$htmlname='fk_tables',$htmloption='',$showempty=0,$showlabel=0,$table="01",$amount=0,$help=0)
{
	global $db, $langs, $conf,$user;
	$sql = "SELECT f.rowid, f.range_ini, f.range_fin, f.codetable AS code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_tables AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " AND f.tabledb = '".$table."'";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';

	if ($selected <> 0 && $selected == '-1')
	{
		if ($showlabel > 0)
		{
			return $langs->trans('To be defined');
		}
	}

	if ($resql)
	{
		$html.= '<select class="form-control" name="'.$htmlname.'" id="select'.$htmlname.'" '.$htmloption.'>';
		if ($showempty)
		{
			$html.= '<option value="0">&nbsp;</option>';
		}

		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				if (!empty($selected) && $selected == $obj->rowid)
				{
					if ($showlabel)
					{
						return $obj->libelle;
					}
					if (!empty($amount))
					{
						if ($amount > $obj->range_ini && $amount <= $obj->range_fin)
							$html.= '<option value="'.$obj->rowid.'" selected="selected">'.$obj->libelle.'</option>';
					}
					else
						$html.= '<option value="'.$obj->rowid.'" selected="selected">'.$obj->libelle.'</option>';
				}
				else
				{
					if (!empty($amount))
					{
						if ($amount > $obj->range_ini && $amount <= $obj->range_fin)
							$html.= '<option value="'.$obj->rowid.'">'.$obj->libelle.'</option>';
					}
					else
						$html.= '<option value="'.$obj->rowid.'">'.$obj->libelle.'</option>';
				}
				$i++;
			}
		}
		$html.= '</select>';
		if ($user->admin && $help) $html.= info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);
		return $html;
	}
}

function select_tables_div($selected='',$id,$table="01",$amount=0)
{
	global $db, $langs, $conf,$user;
	$sql = "SELECT f.rowid, f.range_ini, f.range_fin, f.codetable AS code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_tables AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " AND f.tabledb = '".$table."'";
	$sql.= " ORDER BY f.range_ini, f.range_fin";
	$resql = $db->query($sql);

	//titulo
	$html = '<div>';
	$html.= $langs->trans('Tovalidateselectmode');
	$html.= ': ';
	$html.= '</div>';
	$html.= '<div class="clearfloat;"></div>';
	$html.= '<div>';

	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				if (!empty($selected) && $selected == $obj->rowid)
				{
					if (!empty($amount))
					{
						if ($amount > $obj->range_ini && $amount <= $obj->range_fin)
						{
							$html.= '<a class="linkall" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&fk_type_con='.$rowid.'&action=validateprev'.'" title="'.$label.'">';

							$html.= '<div class=" height80 width90 padding1 margin5 floatleftm3 yellowblack" style="text-align:center;';
							if ($poslk && $l == $poslk)
								$html.= ' border-style: solid; border-color:red; border-width: 1px;';
							$html.= '">';
							$html.= $obj->libelle;
							$html.= '</div>';
							$html.= '</a>';
						}
					}
				}
				else
				{

					if (!empty($amount))
					{

						if ($amount > $obj->range_ini && $amount <= $obj->range_fin)
						{
							$html.= '<a class="linkall" href="'.$_SERVER['PHP_SELF'].'?id='.$id.'&fk_type_con='.$obj->rowid.'&action=validateprev'.'" title="'.$obj->libelle.' : '.$langs->trans('Of').' '.$obj->range_ini.' '.$langs->trans('To').' '.$obj->range_fin.'">';

							$html.= '<div class=" height80 width90 padding1 margin5 floatleftm3 yellowblack" style="text-align:center;';
							if ($poslk && $l == $poslk)
								$html.= ' border-style: solid; border-color:red; border-width: 1px;';
							$html.= '">';
							$html.= $obj->libelle;
							$html.= '</div>';
							$html.= '</a>';
						}
					}
				}
				$i++;
			}
		}
		$html.= '</div>';
		return $html;
	}

}

function fetch_tables($id)
{
	global $db, $langs, $conf;
	$sql = " SELECT f.rowid, f.tabledb, f.codetable, f.label, ";
	$sql.= " f.type, f.range_ini, f.range_fin, f.active ";
	$sql.= " FROM ".MAIN_DB_PREFIX."c_tables AS f ";
	$sql.= " WHERE ";
	$sql.= " f.rowid = ".$id;
	$resql = $db->query($sql);

	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			$obj = $db->fetch_object($resql);
			$aArray['id'] = $obj->rowid;
			$aArray['tabledb'] = $obj->tabledb;
			$aArray['codetable'] = $obj->codetable;
			$aArray['label'] = $obj->label;
			$aArray['type'] = $obj->type;
			$aArray['range_ini'] = $obj->range_ini;
			$aArray['range_fin'] = $obj->range_fin;
			$aArray['active'] = $obj->active;
			return $aArray;

		}
		return 1;
	}
	else
		return -1;
}


function search_tables_amount($table,$amount)
{
	global $db, $langs, $conf;
	$sql = "SELECT f.rowid, f.range_ini, f.range_fin, f.codetable AS code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_tables AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " AND f.tabledb = '".$table."'";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$id = '';

	if ($resql)
	{
		$num = $db->num_rows($resql);
		if ($num)
		{
			$i = 0;
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				//echo '<hr>i '.$i.' '.$amount . ' '.$obj->range_ini.' '.$obj->range_fin;
				if ($amount >= $obj->range_ini && $amount <= $obj->range_fin)
				{
			//  echo '<hr>adentro';
					$id = $obj->rowid;
					$i = $num;
				}
				// else
				// 	echo '<hr>no comprende';
				$i++;
			}
		}
		return $id;
	}
}

function array_all_user($admin=0,$name='')
{
	global $db, $langs, $conf;
	$sql = " SELECT f.rowid, f.login, f.lastname, f.firstname ";
	$sql.= " FROM ".MAIN_DB_PREFIX."user AS f ";
	$sql.= " WHERE ";
	$sql.= " f.entity = ".$conf->entity;
	if (!$admin)
		$sql.= " OR f.entity = 0";
	if (empty($name))
		$sql.= " ORDER BY f.rowid";
	elseif ($name == 'login')
		$sql.= " ORDER BY f.login";
	elseif ($name == 'firstname')
		$sql.= " ORDER BY f.firstname, f.lastname";
	elseif ($name == 'lastname')
		$sql.= " ORDER BY f.lastname, f.firstname";

	$resql = $db->query($sql);

	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while($i < $num)
			{
				$obj = $db->fetch_object($resql);
				if (empty($name))
					$aArray[$obj->rowid] = $obj->rowid;
				elseif($name == 'login')
					$aArray[$obj->rowid] = $obj->login;
				elseif($name == 'firstname')
					$aArray[$obj->rowid] = $obj->firstname.' '.$obj->lastname;
				elseif($name == 'lastname')
					$aArray[$obj->rowid] = $obj->lastname.' '.$obj->firstname;
				$i++;
			}
			return $aArray;
		}
		return 1;
	}
	else
		return -1;
}

function select_month($selected='',$htmlname='mes',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
	global $conf,$langs;

	$langs->load("contab@contab");

	$out='';
	$countryArray=array();
	$label=array();
	$i = 1;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Jan');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Feb');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Mar');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Apr');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('May');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Jun');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Jul');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Aug');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Sep');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Oct');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Nov');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Dec');
	$label[$i] = $countryArray[$i]['rowid'];

	if ($showLabel)
		return $countryArray[$selected]['label'];
	$out = print_selectpoa($selected,$htmlname,$htmloption,$maxlength,
		$showempty,$showLabel,$countryArray,$label);

	return $out;
}

function select_days($selected='',$htmlname='days',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0)
{
	global $conf,$langs;

	$langs->load("poa@poa");

	$out='';
	$countryArray=array();
	$label=array();
	$i = 1;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Monday');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Tuesday');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Wednesday');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Thursday');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Friday');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Saturday');
	$label[$i] = $countryArray[$i]['rowid'];
	$i++;
	$countryArray[$i]['rowid'] = $i;
	$countryArray[$i]['label'] = $langs->trans('Sunday');
	$label[$i] = $countryArray[$i]['rowid'];

	if ($showLabel)
		return $countryArray[$selected]['label'];
	$out = print_selectpoa($selected,$htmlname,$htmloption,$maxlength,
		$showempty,$showLabel,$countryArray,$label);

	return $out;
}

function select_financer($selected='',$htmlname='fk_financer',$htmloption='',$showempty=0,$showlabel=0)
{
	global $db, $langs, $conf,$user;
	if ($showlabel && empty($selected)) return '';
	$sql = "SELECT f.rowid, f.code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_poa_financer AS f ";
	// $sql.= " WHERE ";
	// $sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';

	if ($resql)
	{
		$html.= '<select class="flat" name="'.$htmlname.'">';
		if ($showempty)
		{
			$html.= '<option value="0">&nbsp;</option>';
		}

		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				if (!empty($selected) && $selected == $obj->code)
				{
					$html.= '<option value="'.$obj->code.'" selected="selected">'.$obj->libelle.'</option>';
					if ($showlabel)
					{
						return $obj->libelle;
					}
				}
				else
				{
					$html.= '<option value="'.$obj->code.'">'.$obj->libelle.'</option>';
				}
				$i++;
			}
		}
		$html.= '</select>';
		if ($user->admin) $html.= info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);

		return $html;
	}
}

function select_actors($selected='',$htmlname='fk_actor',$htmloption='',$showempty=0,$showlabel=0,$campo='code',$result='libelle')
{
	global $db, $langs, $conf,$user;
	if ($showlabel && empty($selected)) return '';
	$sql = "SELECT f.rowid, f.code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_poa_actors AS f ";
	$sql.= " WHERE ";
	$sql.= " f.entity = ".$conf->entity;
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';
	$lLabel = false;
	if ($showlabel)
		$lLabel = true;
	if ($resql)
	{
		$html.= '<select class="flat" name="'.$htmlname.'">';
		if ($showempty)
		{
			$html.= '<option value="0">&nbsp;</option>';
		}

		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				if (!empty($selected) && $selected == $obj->$campo)
				{
					$html.= '<option value="'.$obj->$campo.'" selected="selected">'.$obj->$result.'</option>';
					if ($showlabel)
					{
						return $obj->$result;
					}
				}
				else
				{
					$html.= '<option value="'.$obj->$campo.'">'.$obj->$result.'</option>';
				}
				$i++;
			}
		}
		else
		{
			if ($showlabel)
				return '';
		}
		if ($lLabel)
			return 'Err';
		$html.= '</select>';
		if ($user->admin) $html.= info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);

		return $html;
	}
}

function select_requirementtype($selected='',$htmlname='code_requirement',$htmloption='',$showempty=0,$showlabel=0,$campo='code')
{
	global $db, $langs, $conf,$user;
	if ($showlabel && empty($selected)) return '';
	$sql = "SELECT f.rowid, f.code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_poa_requirement AS f ";
	// $sql.= " WHERE ";
	// $sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';
	if ($resql)
	{
		$html.= '<select class="form-control" name="'.$htmlname.'">';
		if ($showempty)
		{
			$html.= '<option value="0">&nbsp;</option>';
		}

		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				if (!empty($selected) && $selected == $obj->$campo)
				{
					$html.= '<option value="'.$obj->$campo.'" selected="selected">'.$obj->libelle.'</option>';
					if ($showlabel)
					{
						return $obj->libelle;
					}
				}
				else
				{
					$html.= '<option value="'.$obj->$campo.'">'.$obj->libelle.'</option>';
				}
				$i++;
			}
		}
		$html.= '</select>';
		if ($user->admin) $html.= info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);

		return $html;
	}
}

function select_code_guarantees($selected='',$htmlname='code_guarantee',$htmloption='',$showempty=0,$showlabel=0,$campo='code')
{
	global $db, $langs, $conf,$user;
	if ($showlabel && empty($selected)) return '';
	$sql = "SELECT f.rowid, f.code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_poa_guarantees AS f ";
	// $sql.= " WHERE ";
	// $sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';

	if ($resql)
	{
		$html.= '<select class="flat form-control" name="'.$htmlname.'">';
		if ($showempty)
		{
			$html.= '<option value="0">&nbsp;</option>';
		}

		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				if (!empty($selected) && $selected == $obj->$campo)
				{
					$html.= '<option value="'.$obj->$campo.'" selected="selected">'.$obj->libelle.'</option>';
					if ($showlabel)
					{
						return $obj->libelle;
					}
				}
				else
				{
					$html.= '<option value="'.$obj->$campo.'">'.$obj->libelle.'</option>';
				}
				$i++;
			}
		}
		$html.= '</select>';
		if ($user->admin) $html.= info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);

		return $html;
	}
}

function select_code_appoint($selected='',$htmlname='code_appoint',$htmloption='',$showempty=0,$showlabel=0,$campo='code')
{
	global $db, $langs, $conf,$user;
	if ($showlabel && empty($selected)) return '';
	$sql = "SELECT f.rowid, f.code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_poa_appoint AS f ";
	// $sql.= " WHERE ";
	// $sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';

	if ($resql)
	{
		$html.= '<select class="flat form-control" name="'.$htmlname.'">';
		if ($showempty)
		{
			$html.= '<option value="0">&nbsp;</option>';
		}

		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				if (!empty($selected) && $selected == $obj->$campo)
				{
					$html.= '<option value="'.$obj->$campo.'" selected="selected">'.$obj->libelle.'</option>';
					if ($showlabel)
					{
						return $obj->libelle;
					}
				}
				else
				{
					$html.= '<option value="'.$obj->$campo.'">'.$obj->libelle.'</option>';
				}
				$i++;
			}
		}
		else {
			return '';
		}
		$html.= '</select>';
		if ($user->admin) $html.= info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);

		return $html;
	}
}

function array_appoint($campo='code')
{
	global $db, $langs, $conf,$user;
	$sql = "SELECT f.rowid, f.code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_poa_appoint AS f ";
	// $sql.= " WHERE ";
	// $sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$array = array();
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$array[$obj->$campo] = $obj->libelle;
				$i++;
			}
			return $array;
		}
	}
	return $array;
}
function print_selectpoa($selected='',$htmlname='status',$htmloption='',$maxlength=0,$showempty=0,$showLabel=0,$countryArray,$label,$loked=0)
{
	if ($loked)
		$htmlloked = 'disabled="disabled"';
	$out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.' '.$htmlloked.'>';
	if ($showempty)
	{
		$out.= '<option value="-1"';
		if ($selected == -1) $out.= ' selected="selected"';
		$out.= '>&nbsp;</option>';
	}

	array_multisort($label, SORT_ASC, $countryArray);

	foreach ($countryArray as $row)
	{
			//print 'rr'.$selected.'-'.$row['label'].'-'.$row['code_iso'].'<br>';
		if ($selected && $selected != '-1' && ($selected == $row['rowid'] || $selected == $row['label']) )
		{
			$foundselected=true;
			$out.= '<option value="'.$row['rowid'].'" selected="selected">';
		}
		else
		{
			$out.= '<option value="'.$row['rowid'].'">';
		}
		$out.= dol_trunc($row['label'],$maxlength,'middle');
		$out.= '</option>';
	}
	$out.= '</select>';

	return $out;
}



///////////////////////////////////////////////////////////////////
function select_type_campo($selected='',$htmlname='type_campo',$htmloption='',$showempty=0,$showlabel=0)
{
	global $db, $langs, $conf,$user;
	$sql = "SELECT f.rowid, f.code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_type_campo AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';

	if ($resql)
	{
		$html.= '<select class="flat" name="'.$htmlname.'">';
		if ($showempty)
		{
			$html.= '<option value="0">&nbsp;</option>';
		}

		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				if (empty($selected) && $showlabel)
				{
					print '&nbsp;';
					return;
				}
				if (!empty($selected) && $selected == $obj->code)
				{
					$html.= '<option value="'.$obj->code.'" selected="selected">'.$obj->libelle.'</option>';
					if ($showlabel)
					{
						print $obj->libelle;
						return;
					}
				}
				else
				{
					$html.= '<option value="'.$obj->code.'">'.$obj->libelle.'</option>';
				}
				$i++;
			}
		}
		$html.= '</select>';
		if ($user->admin) $html.= info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);

		return $html;
	}
}

function select_generic($resql,$showempty='',$htmlname='',$htmloption='',$campo='',$selected='',$nodefined='')
{
	global $db,$langs,$conf;
	$out.= '<select id="select'.$htmlname.'" class="flat selectpays" name="'.$htmlname.'" '.$htmloption.'>';
	if ($showempty)
	{
		$out.= '<option value="-1"';
		if ($selected == -1) $out.= ' selected="selected"';
		$out.= '>&nbsp;</option>';
	}
	if ($nodefined)
	{
		$out.= '<option value="-2"';
		if ($selected == -2) $out.= ' selected="selected"';
		$out.= '>'.$langs->trans('Internalassignment').'</option>';
	}

	$num = $db->num_rows($resql);
	$i = 0;
	if ($num)
	{
		$foundselected=false;

		while ($i < $num)
		{
			$obj = $db->fetch_object($resql);
			$countryArray[$i]['rowid'] 		= $obj->rowid;
			$countryArray[$i]['code_iso'] 	= $obj->code_iso;
			$countryArray[$i]['label']		= ($obj->code_iso && $langs->transnoentitiesnoconv($campo.$obj->code_iso)!=$campo.$obj->code_iso?$langs->transnoentitiesnoconv($campo.$obj->code_iso):($obj->label!='-'?$obj->label:''));
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
	return $out;
}

function generarcodigo($longitud)
{
	$key = '';
	$pattern = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	$max = strlen($pattern)-1;
	for($i=0; $i < $longitud; $i++)
	{
		$key .= $pattern{mt_rand(0,$max)};
	}
	return $key;
}

function param_email()
{
	global $conf;
	//parametros de correos email
	$lUseMailEmp = $conf->global->MANT_USE_EXTENSION_MAIL_COMPANY;
	$mailDefault = $conf->global->MANT_EXTENSION_MAIL_DEFAULT;
	if ($lUseMailEmp == 1)
	{
		$_SESSION['mailEmp'] = true;
		$amailsociete = explode('@',$conf->global->MAIN_INFO_SOCIETE_MAIL);
		$_SESSION['mailDefault'] = $amailsociete[1];
	}
	else
	{
		if (!empty($mailDefault))
		{
			$_SESSION['mailDefault'] = $mailDefault;
		}
		else
		{
			unset($_SESSION['mailDefault']);
		}
	}
	$mailDefault = $_SESSION['mailDefault'];
	return $mailDefault;
}

function fetch_typent($id)
{
	global $db, $langs, $conf;
	$sql = "SELECT f.id, f.code, f.libelle FROM ".MAIN_DB_PREFIX."c_typent AS f ";
	$sql.= " WHERE ";
	$sql.= " f.id = ".$id;

	$resql = $db->query($sql);

	if ($resql)
	{
		if ($db->num_rows($resql))
		{
			$obj = $db->fetch_object($resql);
			return $obj;
		}
		return 0;
	}
	return -1;
}

function htmlsendemail($id,$code,$url)
{
	global $object,$langs,$objAdherent;
	//  $url = $dolibarr_main_url_root;

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/mant/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p>'.$langs->trans('Se ha generado el Ticket Nro.: ').$object->ref.',</p>';
	$html.= '<p>'.$langs->trans('Para el usuario con correo: ').$object->email.',</p>';
	//  $html.= '<p>'.$langs->trans('Interno: ').$object->internal.'.</p>';

	//  $html.= '<p>'.$langs->trans('En un momento se contactará el técnico de turno.').'</p>';

	$html.='<p>'.$langs->trans('Si no ha llenado sus datos, puede acceder al formulario correspondiente a travez del siguiente enlace').' <a href="'.$url.'/mant/jobs/ficheemail.php?action=edit&id='.$object->id.'&code='.$code.'">'.$langs->trans('Ticket').'</a></p>';

	$html.='<br><p>'.$langs->trans('Para hacer seguimiento, por favor haga uso del siguiente enlace').' <a href="'.$url.'/mant/jobs/ficheseek.php?action=search&ref='.$object->ref.'&code='.$code.'">'.$langs->trans('Tickettracing').'</a></p>';

	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
	$html.= '<p>'.$langs->trans('Gerencia de Administración').'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

function htmlsendemailrech($id,$text,$url)
{
	global $object,$langs,$objAdherent;
	//  $url = $dolibarr_main_url_root;

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/mant/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p>'.$langs->trans('Se ha rechazado la Orden de Trabajo Nro.: ').$object->ref.',</p>';
	$html.= '<p>'.$langs->trans('Para el usuario con correo: ').$object->email.',</p>';

	$html.= '<p>'.$text.'</p>';

	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
	$html.= '<p>'.$langs->trans('Gerencia de Administración').'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

function htmlsendemailprog($id,$text,$url)
{
	global $object,$langs,$objAdherent;
	//  $url = $dolibarr_main_url_root;

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/mant/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p>'.$langs->trans('Se ha programado la Orden de Trabajo Nro.: ').$object->ref.',</p>';
	$html.= '<p>'.$langs->trans('Para el usuario con correo: ').$object->email.',</p>';

	$html.= $text;

	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
	$html.= '<p>'.$langs->trans('Gerencia de Administración').'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

function htmlsendemailassign($id,$text,$url)
{
	global $object,$langs,$objAdherent;
	//  $url = $dolibarr_main_url_root;

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/mant/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p>'.$langs->trans('Se ha asignado la Orden de Trabajo Nro.: ').$object->ref.',</p>';
	$html.= '<p>'.$langs->trans('Para el usuario solicitante con correo: ').$object->email.',</p>';

	$html.= $text;

	$html.='<br><p>'.$langs->trans('Hacer clic en el siguiente enlace').' <a href="'.$url.'/mant/jobs/fiche.php?ref='.$object->ref.'">'.$langs->trans('Jobs').'</a></p>';

	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
	$html.= '<p>'.$langs->trans('Gerencia de Administración').'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

function htmlsendemailjob($id,$text,$url)
{
	global $object,$langs,$objAdherent;
	//  $url = $dolibarr_main_url_root;

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/mant/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p>'.$langs->trans('Se ha concluido la Orden de Trabajo Nro.: ').$object->ref.',</p>';
	$html.= '<p>'.$langs->trans('Para el usuario con correo: ').$object->email.',</p>';

	$html.= $text;

	$html.='<br><p>'.$langs->trans('Para conformidad, rogamos hacer clic en el siguiente enlace').' <a href="'.$url.'/mant/jobs/ficheemail.php?action=confirm&ref='.$object->ref.'&code='.$object->tokenreg.'">'.$langs->trans('Compliance work order').'</a></p>';

	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
	$html.= '<p>'.$langs->trans('Gerencia de Administración').'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

function htmlsendemailconfirm($id,$text,$url)
{
	global $object,$langs,$objAdherent;
	//  $url = $dolibarr_main_url_root;

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/mant/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	if ($object->statut_job == 1)
		$html.= '<p>'.$langs->trans('Compliance work order').': '.$object->ref.',</p>';
	if ($object->statut_job == 2)
		$html.= '<p>'.$langs->trans('Nonconformity of the work order').': '.$object->ref.',</p>';

	$html.= '<p>'.$langs->trans('del usuario con correo: ').$object->email.',</p>';

	$html.= $text;

	$html.='<br><p>'.$langs->trans('Hacer clic en el siguiente enlace').' <a href="'.$url.'/mant/jobs/fiche.php?ref='.$object->ref.'">'.$langs->trans('Jobs').'</a></p>';

	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
	$html.= '<p>'.$langs->trans('Gerencia de Administración').'</p>';

	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

function pei_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('poa');
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/poa/pei/card.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Fiche");
	$head[$h][2] = 'card';
	$h++;

	$head[$h][0] = dol_buildpath("/poa/pei/objetive.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Objetivesstrategic");
	$head[$h][2] = 'objetive';
	$h++;
	$head[$h][0] = dol_buildpath("/poa/objetive/objetive.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Management Objective");
	$head[$h][2] = 'objetivegestion';
	$h++;
	//$head[$h][0] = dol_buildpath("/poa/objetive/structure.php?id=".$object->id,1);
	//$head[$h][1] = $langs->trans("Catprog");
	//$head[$h][2] = 'catprog';
	//$h++;
	//$head[$h][0] = dol_buildpath("/poa/objetive/rrhh.php?id=".$object->id,1);
	//$head[$h][1] = $langs->trans("RRHH");
	//$head[$h][2] = 'rrhh';
	//$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'pei');

	return $head;
}

function objetiveinp_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('poa');
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/poa/objetive/listinp.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Management Income Objetive");
	$head[$h][2] = 'objetivegestion';
	$h++;
	$head[$h][0] = dol_buildpath("/poa/objetive/structureinp.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Catprog");
	$head[$h][2] = 'catprog';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'objetiveinp');

	return $head;
}

function objetiveout_prepare_head($object)
{
	global $langs, $conf,$user;
	$langs->load('poa');
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/poa/objetive/listout.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Management Spending target");
	$head[$h][2] = 'objetivegestion';
	if ($user->rights->poa->objstr->read)
	{
		$h++;
		$head[$h][0] = dol_buildpath("/poa/objetive/structure.php?id=".$object->id,1);
		$head[$h][1] = $langs->trans("Catprog");
		$head[$h][2] = 'catprog';
	}
	if ($user->rights->poa->proout->read)
	{
		$h++;
		$head[$h][0] = dol_buildpath("/poa/objetive/rrhh.php?id=".$object->id,1);
		$head[$h][1] = $langs->trans("RRHH");
		$head[$h][2] = 'rrhh';

		$h++;
		$head[$h][0] = dol_buildpath("/poa/objetive/partidaproduct.php?id=".$object->id,1);
		$head[$h][1] = $langs->trans("Materials and supplies");
		$head[$h][2] = 'product';
		$h++;
		$head[$h][0] = dol_buildpath("/poa/objetive/partidaasset.php?id=".$object->id,1);
		$head[$h][1] = $langs->trans("Activos fijos");
		$head[$h][2] = 'asset';
		$h++;
		$head[$h][0] = dol_buildpath("/poa/objetive/partidaservice.php?id=".$object->id,1);
		$head[$h][1] = $langs->trans("Services");
		$head[$h][2] = 'service';
		$h++;
		$head[$h][0] = dol_buildpath("/poa/objetive/partidaother.php?id=".$object->id,1);
		$head[$h][1] = $langs->trans("Others");
		$head[$h][2] = 'other';
	}
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'objetiveout');

	return $head;
}

function equipment_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('mant');
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/mant/equipment/fiche.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Fiche");
	$head[$h][2] = 'Ficha';
	$h++;

	$head[$h][0] = dol_buildpath("/mant/equipment/programming.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Programming");
	$head[$h][2] = 'Programming';
	$h++;


	return $head;
}

function get_partida($gestion)
{
	global $db, $langs, $conf;
	$sql = "SELECT f.rowid, f.code AS code, f.label AS libelle ";
	$sql.=" FROM ".MAIN_DB_PREFIX."c_partida AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " AND f.period_year = ".$gestion;
	$sql.= " ORDER BY f.code";
	$resql = $db->query($sql);
	$array = array();
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$array[$obj->code] = $obj->libelle;
				$i++;
			}
		}
		return $array;
	}
}

function tipo_grafico($aLimite,$monto)
{
	if (empty($monto))
		return '';
	foreach($aLimite AS $i => $valor)
	{
		if ($monto > $valor)
		{
			$escala = $i;
			return $i;
		}
	}
	return $i;
}

function randomColor()
{
	$str= '#';
	for ($i = 0; $i < 6; $i++)
	{
		$randNum = rand (7,15);
		switch($randNum)
		{
			case 10: $randNum = 'A'; break;
			case 11: $randNum = 'B'; break;
			case 12: $randNum = 'C'; break;
			case 13: $randNum = 'D'; break;
			case 14: $randNum = 'E'; break;
			case 15: $randNum = 'F'; break;
		}
		$str.=$randNum;
	}
	return $str;
}

function porcGrafico($aBalance,$valor)
{
	if ($valor == 0)
		return 1;
	foreach((array) $aBalance AS $i => $aValue)
	{
		if ($valor > $aValue[0] && $valor <= $aValue[1])
			return $i;
	}
	return '';
}

function bodypacemail($obj,$url,$nameto='')
{
	global $langs;
	//  $url = $dolibarr_main_url_root;
	$outputlangs = $langs;
	$monthArray = monthArray($outputlangs);

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	$html.= '<link rel="stylesheet" media="screen" href="'.$url.'/poa/css/style-email.css">';
	$html.= '<meta Content-type: text/html; charset= iso-8859-1>';

	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p>'.$langs->trans('Nombre PAC').': '.$obj->nom.',</p>';
	$html.= '<p>'.$langs->trans('Mes inicio').': '.$monthArray[$obj->month_init].',</p>';

	$html.= '<p>'.$langs->trans('Responsable').': '.$nameto.',</p>';
	$html.= '<p>'.$langs->trans('El proceso no se encuentra iniciado, favor rogamos informar el estado del mismo').'.</p>';

	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
	$html.= '<p>'.$langs->trans('Gerencia de Administración').'</p>';
	$html.= '<p>'.$langs->trans('DMMI').'</p>';
	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

function bodyprevemail($obj,$url,$nameto='')
{
	global $langs;
	//  $url = $dolibarr_main_url_root;
	$outputlangs = $langs;
	$monthArray = monthArray($outputlangs);

	$html = '<!DOCTYPE HTML>';
	$html.= '<html>';
	$html.= '<head>';
	//  $html.= '<link rel="stylesheet" media="screen" href="'.$url.'/poa/css/style-email.css">';
	$html.= '<meta http-equiv="Content-type" content="text/html; charset=UTF-8">';
	$html.= '</head>';
	$html.= '<body>';
	$html.= '<p style="color:#ff0000;">'.$langs->trans('Nro.').' '.$langs->trans('Preventive').': '.$obj->nro_preventive.',</p>';
	$html.= '<p>'.$langs->trans('Name').': '.$obj->label.'.</p>';
	$html.= '<p>'.$langs->trans('Date').': '.dol_print_date($obj->date_preventive,'day').',</p>';

	$html.= '<p>'.$langs->trans('Responsable').': '.$nameto.',</p>';
	$html.= '<p>'.$langs->trans('El preventivo de referencia, fue creado en fecha '.dol_print_date($obj->date_preventive,'day').'.').'</p>';
	$html.= '<p>'.$langs->trans('En la fecha no tiene movimiento alguno, favor rogamos revisar y enviar respuesta respecto al estado del mismo.').'</p>';
	$html.= '<p>'.$langs->trans('Atentamente,').'</p>';
	$html.= '<p>'.$langs->trans('Gerencia de Administración').'</p>';
	$html.= '<p>'.$langs->trans('DMMI').'</p>';
	$html.= '</body>';
	$html.= '</html>';
	return $html;
}

function user_active_poa($obj)
{
	global $objectuser,$objuser;
	//verifica usuario activo
	$newNombre = '';
	$objectuser->fetch_active($obj->id);
	if ($objectuser->id > 0 && $obj->id == $objectuser->fk_poa_poa)
	{
		$newNombre = '';
		$objuser->fetch($objectuser->fk_user);
		$nombre = $objuser->firstname.' '.$objuser->lastname;
		$aNombre = explode(' ',$nombre);
		foreach($aNombre AS $k => $value)
		{
			$newNombre .= substr($value,0,1);
		}
	}
	return $newNombre;
}

function userid_active_poa($obj)
{
	global $objectuser,$objuser;
	//verifica usuario activo
	$idUser = 0;
	$newNombre = '';
	$objectuser->fetch_active($obj->id);
	if ($objectuser->id > 0 && $obj->id == $objectuser->fk_poa_poa)
	{
		$idUser = $objectuser->fk_user;
	}
	return $idUser;
}

function assign_filter_user($search_user)
{
	global $user,$objuser;
	if (!$user->admin)
	{

		$search_area = '';
		if (!isset($_SESSION[$search_user]))
		{
		//verifica usuario activo
			$newNombre = '';
			$objuser->fetch($user->id);
			$nombre = $objuser->login;
			$_SESSION[$search_user] = STRTOUPPER($nombre);

			$aUserpriv = array();
			if (!empty($_SESSION['userpriv']))
				$aUserpriv = $_SESSION['userpriv'];
			foreach ((array)$aUserpriv AS $idArea => $privilege)
			{
				if (!empty($search_area)) $search_area.=',';
				$search_area.=$idArea;

				if ($privilege == 3 || $privilege == 1)
					$_SESSION[$search_user] = '';
			}

		}
	}
	return;
}


function filter_area_user($id,$lview=false)
{
	global $db,$user;

	require_once(DOL_DOCUMENT_ROOT."/poa/area/class/poaareauser.class.php");

	$aArea = array();
	$idsArea = '';
	if (!$user->admin || $lview)
	{
		$objareauser = new Poaareauser($db);
		$aArea = $objareauser->getuserarea($id);
		foreach((array) $aArea AS $j => $objarea)
		{
			if (!empty($idsArea)) $idsArea.=',';
			$idsArea.= $j;
		}
	}
	return $idsArea;
}

function diahabil($fechaInicial,$MaxDias=1)
{
	$Segundos = $fechaInicial;
	//Creamos un for desde 0 hasta 3
	for ($i=0; $i<$MaxDias; $i++)
	{
		$Segundos = $Segundos + 86400;
		$caduca = date("D",$Segundos);
		if ($caduca == "Sat")
			$i--;
		else if ($caduca == "Sun")
			$i--;
		else
			$FechaFinal = date("Y-m-d",$Segundos);
	}
	return $Segundos;
}
function diacalend($fechaInicial,$MaxDias)
{
	$Segundos = $fechaInicial;
	//Creamos un for desde 0 hasta 3
	for ($i=0; $i<$MaxDias; $i++)
		$Segundos = $Segundos + 86400;
	return $Segundos;
}

function select_typeprocedure($selected='',$htmlname='code_procedure',$htmloption='',$showempty=0,$showlabel=0,$campo='rowid',$label='label')
{
	global $db, $langs, $conf,$user;
	$sql = "SELECT f.rowid, f.code, f.label, f.sigla FROM ".MAIN_DB_PREFIX."c_typeprocedure AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " ORDER BY f.label";
	$resql = $db->query($sql);
	$html = '';
	//echo '<br>sel '.$selected;
	if ($selected <> 0 && $selected == '-1')
	{
		if ($showlabel > 0)
		{
			return $langs->trans('To be defined');
		}
	}

	if ($resql)
	{
		$html.= '<select class="flat" name="'.$htmlname.'" id="select'.$htmlname.'">';
		if ($showempty)
		{
			$html.= '<option value="0">&nbsp;</option>';
		}

		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				if (!empty($selected) && $selected == $obj->$campo)
				{
					$html.= '<option value="'.$obj->$campo.'" selected="selected">'.$obj->$label.'</option>';
					if ($showlabel)
					{
						return $obj->$label;
					}
				}
				else
				{
					$html.= '<option value="'.$obj->$campo.'">'.$obj->$label.'</option>';
				}
				$i++;
			}
		}
		else
		{
			return '';
		}
		if ($showlabel)
			return '';
		$html.= '</select>';
		if ($user->admin) $html.= info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionarySetup"),1);

		return $html;
	}
}

function fetch_typeprocedure($selected,$campo='rowid')
{
	global $db, $langs, $conf;
	if (empty($selected))
		return -1;
	$sql = "SELECT f.rowid, f.code, f.label, f.landmark, f.colour, f.active FROM ".MAIN_DB_PREFIX."c_typeprocedure AS f ";
	$sql.= " WHERE ";
	$sql.= " f.active = 1";
	$sql.= " AND ".$campo." = '".$selected."'";
	$resql = $db->query($sql);
	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			$obj = $db->fetch_object($resql);
			return $obj;
		}
		else
			return -1;
	}
}

//$campo = rowid
//$campo = code
function getlist_typeprocedure($campo,$active=1,$order='f.label')
{
	global $db, $langs, $conf;
	$sql = "SELECT f.rowid, f.code, f.label, f.sigla, f.landmark, f.colour FROM ".MAIN_DB_PREFIX."c_typeprocedure AS f ";
	$sql.= " WHERE ";
	if ($active)
		$sql.= " f.active = ".$active;
	else
		$sql.= " f.active IN (0,1)";

	$sql.= " ORDER BY ".$order;
	$resql = $db->query($sql);
	$array = array();

	if ($resql)
	{
		$num = $db->num_rows($resql);
		$i = 0;
		if ($num)
		{
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$array[$obj->$campo] = $obj;
				$i++;
			}
		}
		return $array;
	}
}

/*resta fecha
 * $fecha1 = fecha inicial
 * $fecha2 = fecha final
*/
function resta_fechas( $fecha2, $fecha1,$type=0 )
{
	$timeStamp1 = $fecha1;
	$timeStamp2 = $fecha2;
	$restaSeg = $timeStamp1 - $timeStamp2;
	$diaRes = intval($restaSeg/60/60/24);
	if ($type) //dias
	return $diaRes;
	else
		return $restaSeg;
}

function texthtmlworkflow($obj,$day,$dayall)
{
	global $langs;
	// Information
	$procedure = select_typeprocedure($obj->code_procedure,'code_procedure','',0,1,'code');

	$htmltooltip =    ''.$langs->trans("from").': '.$obj->code_area_last;
	$htmltooltip.='<br>'.$langs->trans("To").': '.$obj->code_area_next;
	$htmltooltip.='<br>'.$langs->trans("Date").': '.dol_print_date($obj->date_tracking,'day');
	$htmltooltip.='<br>'.$langs->trans("Action").': '.$procedure;

	$htmltooltip.='<br><br><u>'.$langs->trans("Detail").':</u>';
	$htmltooltip.='<br>'.$langs->trans("Message").': '.$obj->detail;
	$htmltooltip.='<br>'.$langs->trans("Dateread").': '.((is_null($obj->date_read) OR empty($obj->date_read))?'':dol_print_date($obj->date_read.'day'));
	$htmltooltip.='<br>'.$langs->trans("Sequen").': '.$obj->sequen;
	$htmltooltip.='<br>'.$langs->trans("Went by").': '.$day .' de '.$dayall.' '.$langs->trans('Days');

	return $htmltooltip;
	// print '<td align="center">';
	// print $form->textwithpicto('',$htmltooltip,1,0);
	// print '</td>';
}

function classpoa($a)
{
	switch ($a)
	{
		case true:
		$cclass='class="pair1"';
		break;
		default:
		$cclass='class="impair1"';
		break;
	}

	return $cclass;
}

function filterres($aDays,$aColors,$filterw,$daydelay,$cClass,$nDia,$statut = '')
{
	global $objwork;
	if (substr($filterw,1,4) == 0 && $statut == 2)
	{
			//if ($filterw == $daydelay)
		$cClass = '';
		$lContinue = true;
	}
	else
	{
		if ($statut == 2)
			$lContinue = false;
		else
		{
			foreach ((array) $aDays AS $nDay => $aDay)
			{
				if ($daydelay > $aDay[1] && $daydelay <= $aDay[2])
				{
					$cClass = $aColors[$nDay];
		//	  echo '<hr>'.substr($filterw,1,4).'  > '.$aDay[1].' && '.substr($filterw,1,4).' <= '.$aDay[2].' && '.$objwork->statut.' < 2';
					if (substr($filterw,1,4) == 0)
					{
						if ($statut == 2)
					//if ($filterw == $daydelay)
							$lContinue = true;
					}
					elseif (substr($filterw,1,4)  > $aDay[1] && substr($filterw,1,4) <= $aDay[2] )
					{
						$lContinue = true;
					}
				}
			}
		}
	}
	//si el limite es superado
	if(substr($filterw,1,4) >= $nDia && $daydelay >= substr($filterw,1,4))
	{
		$cClass = $aColors[$nDia];
		$lContinue = true;
	}
	// si el filtro es vacio
	if (empty($filterw))
	{
		if ($daydelay > $nDia)
			$cClass = $aColors[$nDia];
	}
	return array($cClass,$lContinue);
}

function defgraf($rango)
{
	$aLimite = array(1=>array(1=>1,2=>25),
		2=>array(1=>26,2=>50),
		3=>array(1=>51,2=>75),
		4=>array(1=>76,2=>100));

	$graf = 0;
	$td1 = ceil($rango);
	if ($td1 <=0)
		$graf = 0;
	else
	{
		foreach((array) $aLimite AS $d => $aLim)
		{
			if ($td1 >= $aLim[1] && $td1 <= $aLim[2])
			{
				$graf = $d;
			}
		}
	}
	return $graf;
}


			//numerico a literal DSO
function num2texto($numero, $moneda = "Bolivianos", $singular = "Boliviano") {
				// Obtenida de www.hackingballz.com
				// Si es 0 el número, no tiene caso procesar toda la información
	if( $numero == 0 || !isset( $numero ) ) {
		return strtoupper( "CERO $moneda 00/100" );
	}
				// En caso que sea un peso, pues igual que el 0 aparte que no muestre el plural "pesos"

	if( $numero == 1 ) {
		return strtoupper( "UN $singular 00/100" );
	}

				//$numeros["unidad"][0][0]="cero";
	$numeros["unidad"][1][0] = "un";
	$numeros["unidad"][2][0] = "dos";
	$numeros["unidad"][3][0] = "tres";
	$numeros["unidad"][4][0] = "cuatro";
	$numeros["unidad"][5][0] = "cinco";
	$numeros["unidad"][6][0] = "seis";
	$numeros["unidad"][7][0] = "siete";
	$numeros["unidad"][8][0] = "ocho";
	$numeros["unidad"][9][0] = "nueve";

	$numeros["decenas"][1][0] = "diez";
	$numeros["decenas"][2][0] = "veinte";
	$numeros["decenas"][3][0] = "treinta";
	$numeros["decenas"][4][0] = "cuarenta";
	$numeros["decenas"][5][0] = "cincuenta";
	$numeros["decenas"][6][0] = "sesenta";
	$numeros["decenas"][7][0] = "setenta";
	$numeros["decenas"][8][0] = "ochenta";
	$numeros["decenas"][9][0] = "noventa";
	$numeros["decenas"][1][1][0] = "dieci";
	$numeros["decenas"][1][1][1] = "once";
	$numeros["decenas"][1][1][2] = "doce";
	$numeros["decenas"][1][1][3] = "trece";
	$numeros["decenas"][1][1][4] = "catorce";
	$numeros["decenas"][1][1][5] = "quince";
	$numeros["decenas"][2][1] = "veinte y ";
	$numeros["decenas"][3][1] = "treinta y ";
	$numeros["decenas"][4][1] = "cuarenta y ";
	$numeros["decenas"][5][1] = "cincuenta y ";
	$numeros["decenas"][6][1] = "sesenta y ";
	$numeros["decenas"][7][1] = "setenta y ";
	$numeros["decenas"][8][1] = "ochenta y ";
	$numeros["decenas"][9][1] = "noventa y ";

	$numeros["centenas"][1][0] = "cien";
	$numeros["centenas"][2][0] = "doscientos ";
	$numeros["centenas"][3][0] = "trecientos ";
	$numeros["centenas"][4][0] = "cuatrocientos ";
	$numeros["centenas"][5][0] = "quinientos ";
	$numeros["centenas"][6][0] = "seiscientos ";
	$numeros["centenas"][7][0] = "setecientos ";
	$numeros["centenas"][8][0] = "ochocientos ";
	$numeros["centenas"][9][0] = "novecientos ";
	$numeros["centenas"][1][1] = "ciento ";

	$postfijos[1][0] = "";
	$postfijos[10][0] = "";
	$postfijos[100][0] = "";
	$postfijos[1000][0] = " mil ";
	$postfijos[10000][0] = " mil ";
	$postfijos[100000][0] = " mil ";
	$postfijos[1000000][0] = " millon ";
	$postfijos[10000000][0] = " millon ";
	$postfijos[100000000][0] = " millon ";
	$postfijos[1000000][1] = " millones ";
	$postfijos[10000000][1] = " millones ";
	$postfijos[100000000][1] = " millones ";

	$decimal_break = ".";
	//echo "test run on ".$numero."<br>";
	$entero = strtok( $numero, $decimal_break);
	$decimal = strtok( $decimal_break );
	if ( $decimal == "" ) {
		$decimal = "00";
	}
	if ( strlen( $decimal ) < 2 ) {
		$decimal .= "0";
	}
	if ( strlen( $decimal ) > 2 ) {
		$decimal = substr( $decimal, 0, 2 );
	}
	$decimal .= '/100';
	$entero_breakdown = $entero;

	$breakdown_key = 1000000000000;
	$num_string = "";
	while ( $breakdown_key > 0.5 ) {
		$breakdown["entero"][$breakdown_key]["number"] =
		floor( $entero_breakdown/$breakdown_key );

		if ( $breakdown["entero"][$breakdown_key]["number"] > 0 ) {
			$breakdown["entero"][$breakdown_key][100] =
			floor( $breakdown["entero"][$breakdown_key]["number"] / 100 );
			$breakdown["entero"][$breakdown_key][10] =
			floor( ( $breakdown["entero"][$breakdown_key]["number"] % 100 )
				/ 10 );
			$breakdown["entero"][$breakdown_key][1] =
			floor( $breakdown["entero"][$breakdown_key]["number"] % 10 );

			$hundreds = $breakdown["entero"][$breakdown_key][100];
							// if not a closed value at hundredths
			if ( ( $breakdown["entero"][$breakdown_key][10]
				+ $breakdown["entero"][$breakdown_key][1] ) > 0 ) {
				$chundreds = 1;
		} else {
			$chundreds = 0;
		}

		if ( isset( $numeros["centenas"][$hundreds][$chundreds] ) ) {
			$num_string .= $numeros["centenas"][$hundreds][$chundreds];
		} else {
			if( isset( $numeros["centenas"][$hundreds][0] ) ) {
				$num_string .= $numeros["centenas"][$hundreds][0];
			}
		}

		if ( ( $breakdown["entero"][$breakdown_key][1] ) > 0 ) {
			$ctens = 1;
			$tens = $breakdown["entero"][$breakdown_key][10];
			if ( ( $breakdown["entero"][$breakdown_key][10] ) == 1 ) {
				if ( ( $breakdown["entero"][$breakdown_key][1] ) < 6 ) {
					$cctens = $breakdown["entero"][$breakdown_key][1];
					$num_string .=
					$numeros["decenas"][$tens][$ctens][$cctens];
				} else {
					$num_string .= $numeros["decenas"][$tens][$ctens][0];
				}
			} else {
				if( isset( $numeros["decenas"][$tens][$ctens] ) ){
					$num_string .= $numeros["decenas"][$tens][$ctens];
				}
			}
		} else {
			$ctens = 0;
			$tens = $breakdown["entero"][$breakdown_key][10];
			if( isset( $numeros["decenas"][$tens][$ctens] ) ) {
				$num_string .= $numeros["decenas"][$tens][$ctens];
			}
		}

		if ( !( isset( $cctens ) ) ) {
			$ones = $breakdown["entero"][$breakdown_key][1];
			if ( isset( $numeros["unidad"][$ones][0] ) ) {
				$num_string .= $numeros["unidad"][$ones][0];
			}
		}

		$cpostfijos = -1;
		if ( $breakdown["entero"][$breakdown_key]["number"] > 1 ) {
			$cpostfijos = 1;
		}

		if ( isset( $postfijos[$breakdown_key][$cpostfijos] ) ) {
			$num_string .= $postfijos[$breakdown_key][$cpostfijos];
		} else {
			$num_string .= $postfijos[$breakdown_key][0];
		}
	}
	unset( $cctens );
	$entero_breakdown %= $breakdown_key;
	$breakdown_key /= 1000;
}
$letras = $num_string . ' ' . $decimal . " $moneda";
$letras = strtoupper( $letras );
return $letras;
}

function dirpoa($cpa,$id,$ref='')
{
	global $conf;
	$dir = '';
	$url = '';
	switch ($cpa)
	{
		case 'PREVENTIVE':
		$dir = $conf->poa->dir_output.'/execution/pdf/'.$id.'.pdf';
		if (is_file($dir))
			$url = DOL_URL_ROOT.'/documents/poa/execution/pdf/'.$id.'.pdf';
		break;
		case 'INI_PROCES':
		$dir = $conf->poa->dir_output.'/process/pdf/'.$id.'.pdf';
		if (is_file($dir))
			$url = DOL_URL_ROOT.'/documents/poa/process/pdf/'.$id.'.pdf';
		break;
		case 'RECEP_PRODUCTS':
		$dir = $conf->contrat->dir_output.'/'.$ref.'/'.$id.'.pdf';
		if (is_file($dir))
			$url = DOL_URL_ROOT.'/documents/contracts/'.$ref.'/'.$id.'.pdf';
		break;
		case 'AUT_PAYMENT':
		$dir = $conf->poa->dir_output.'/payment/pdf/'.$id.'.pdf';
		if (is_file($dir))
			$url = DOL_URL_ROOT.'/documents/poa/payment/pdf/'.$id.'.pdf';
		break;
	}
	return array($dir,$url);
}


//function que agrupa los inicios de procesos en principal y seguncadrio
function getlist_process($fk_prev,$aProcess=array())
{
	global $conf,$objpre,$objproc;
	if (empty($aProcess['actual']))
		$aProcess['actual'] = $fk_prev;
	if ($objpre->fetch($fk_prev)>0)
	{
		if ($objpre->id == $fk_prev)
		{
			if ($objpre->fk_prev_ant>0)
			{
				//buscamos el proceso actual
				if ($objproc->fetch_prev($fk_prev)>0)
					$aProcess['sec'][$objproc->id] = $objproc->fk_poa_prev;
				$aProcess = getlist_process($objpre->fk_prev_ant,$aProcess);
			}
			else
			{
				if ($objproc->fetch_prev($fk_prev)>0)
				{
					if ($objproc->fk_poa_prev == $fk_prev)
						$aProcess['pri'][$objproc->id] = $objproc->fk_poa_prev;
				}
			}
		}
	}
	return $aProcess;
}

//actualiza workflow por el proceso principal
function actualiza_workflow($aProcess)
{
	global $langs, $db, $user;
	require_once(DOL_DOCUMENT_ROOT."/poa/workflow/class/poaworkflow.class.php");
	require_once(DOL_DOCUMENT_ROOT."/poa/workflow/class/poaworkflowdet.class.php");
	$objw = new Poaworkflow($db);
	//recuperamos el workflow del principal
	$idWorkflow = 0;
	foreach ((array )$aProcess['pri'] AS $fk_process => $fk_prev)
	{
		if ($objw->fetch_prev($fk_prev)>0)
			if ($objw->fk_poa_prev == $fk_prev)
				$idWorkflow = $objw->id;
		}

		foreach ((array )$aProcess['sec'] AS $fk_process => $fk_prev)
		{
			if ($objw->fetch_prev($fk_prev)>0)
				if ($objw->fk_poa_prev == $fk_prev)
				{
					foreach ($objw->array_options AS $j => $objd)
					{
		//vamos actulaizando
						$objwd = new Poaworkflowdet($db);
						if ($objwd->fetch($objd->id)>0)
						{
							$objwd->fk_poa_workflow = $idWorkflow;
							$objwd->update($user);
						}
					}
				}
			}
		}

/*
resumen el color
*/
function resumcolor($aDate,$aGrap,$aGrapcode,$aGraptitle,$aResum,$aResumej,$obj,$code,$id,$date='',$label='',$titleact='',$amount=0)
{
	global $langs;
	if ($aDate['mon'])
	{
		$aGrap[$aDate['mon']][$code][$id] = $code;
		$objColor = fetch_typeprocedure($code,'code');
		$aGrapcode[$aDate['mon']][$code][$id] = 'style="background:#'.$objColor->colour.'; float:left; width:8px; text-align:right; height:17px;" ';
		if (empty($aResum[$aDatepre['mon']][$code]))
			$aResum[$aDate['mon']][$code] = 'style="background:#'.$objColor->colour.'; float:left; width:8px; text-align:right; height:17px;" ';
		$title = '<p><b>'.$langs->trans('Title').':</b> '.$titleact;
		$title.='<br><b>'.$langs->trans('Procedure').':</b> '.$objColor->label;
		$title.= '<br><b>'.$langs->trans('Date').':</b> '.dol_print_date($obj->$date,'day');
		$title.= '<br><b>'.$langs->trans('Detail').':</b> '.$obj->$label;
		$title.= '<br><b>'.$langs->trans('Amount').':</b> '.price($amount);
		$title.='</p>';
		$aGraptitle[$aDate['mon']][$code][$id] = $title;
		$aResumej[$aDate['mon']][$code] = $code;
	}
	return array($aGrap,$aGrapcode,$aGraptitle,$aResum,$aResumej);
}

function viewwork($n,$idUser,$obj,$objactwork,$lisPrev)
{
	global $langs,$user,$db;
	$idTagps = $obj->id+100000;
	$idTagps2 = $idTagps+100500;
	$objactwork->fetch_users($obj->id,$idUser);
	$campo = 't'.$n;
	$cWork = '&nbsp;';
	if ($objactwork->fk_activity == $obj->id
		&& $objactwork->fk_user == $idUser)
		$cWork = $objactwork->$campo;
	if ($user->rights->poa->work->crear)
	{
		$lisPrev.=  '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">'.'<input id="'.$idTagps.'_act" type="text" name="t" value="'.$cWork.'" onblur="CambiarURLFramew('.$obj->id.','.$idTagps.','.$n.','.'this.value);" size="6">'.'</span>';

		$lisPrev.= '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick="visual_five('.$idTagps.' , '.$idTagps2.')">';
		$lisPrev.= '<a href="#" title="'.$cWork.'"><div style="width:25px; height:24px;">'.$cWork.'</div></a>';
		$lisPrev.= '</span>';
	}
	else
	{
		$lisPrev.= '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;">';
		$lisPrev.= '<a href="#" title="'.$cWork.'">'.$cWork.'</a>';
		$lisPrev.= '</span>';
	}
	return $lisPrev;
}

function viewpriority($idUser,$obj,$lisPrev)
{
	global $langs,$user,$db;
	$idTagps = $obj->id+100000;
	$idTagps2 = $idTagps+100500;
	$nPriority = $obj->priority;
	if ($user->rights->poa->work->crear)
	{
		$lisPrev.=  '<span id="'.$idTagps.'" style="visibility:hidden; display:none;">'.'<input id="'.$idTagps.'_act" type="text" name="t" value="'.$nPriority.'" onblur="CambiarURLFramep('.$obj->id.','.$idTagps.','.'this.value);" size="6">'.'</span>';

		$lisPrev.= '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;" onclick="visual_five('.$idTagps.' , '.$idTagps2.')">';
		$lisPrev.= '<a href="#" title="'.$nPriority.'"><div style="width:25px; height:24px;">'.$nPriority.'</div></a>';
		$lisPrev.= '</span>';
	}
	else
	{
		$lisPrev.= '<span  id="'.$idTagps2.'" style="visibility:visible; display:block;">';
		$lisPrev.= '<a href="#" title="'.$nPriority.'">'.$nPriority.'</a>';
		$lisPrev.= '</span>';
	}
	return $lisPrev;
}

//function para determinar la fecha final
function date_end($datecontrat,$cod_plazo,$plazo)
{
	if ($cod_plazo==1)
		$datefin = diacalend($datecontrat,$plazo);
	else if ($cod_plazo==2)
		$datefin = diahabil($datecontrat,$plazo);
	return $datefin;
}
//obtiene el preventivo anterior principal
function prev_ant($id, $array,$statutppc=1)
{
	global $db, $langs;
	if (empty($id)) return array();
	require_once DOL_DOCUMENT_ROOT.'/poa/class/poaprevsegext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/poa/class/poaprocessext.class.php';
	$objseg = new Poaprevsegext($db);
	$objpro = new Poaprocessext($db);
	$lret = true;
	$arrayc = array();
	$idsearch = $id;
	$array[$id]['idprev'][$id] = $id;
	$array[$id]['idprevant'] = 0;
	$array[$id]['idprocess'] = 0;
	$objpro->fetch_prev($id,'0,1,2');
	if ($objpro->fk_poa_prev == $id)
	{
		$array[$id]['idprocess'] = $objpro->id;
		$arrayc = listcontrat($objpro->id,$arrayc,$statutppc);
		$array[$id]['contrat'] = $arrayc;
	}
	while($lret == true)
	{
			//buscamos el preventivo
		$objseg->fetch('',$idsearch);
		if ($objseg->fk_prev == $idsearch)
		{
			//verificamos si tiene prev anterior
			if ($objseg->fk_prev_ant > 0)
			{
				$idsearch = $objseg->fk_prev_ant;
				$array[$id]['idprevant'] = $idsearch;
				$array[$id]['idprev'][$idsearch] = $idsearch;

				if ($objpro->fetch_prev($idsearch)>0)
				{
					if ($objpro->fk_poa_prev == $idsearch)
					{
						$array[$id]['idprocessant'] = $objpro->id;
						$arrayc = listcontrat($objpro->id,$arrayc,$statutppc);
					}
				}
			}
			else
				$lret = false;
		}
		else
			$lret = false;
	}
	$array[$id]['contrat'] = $arrayc;

	return $array;
}

function listcontrat($id,$array,$statutppc=1)
{
	global $db;
	require_once DOL_DOCUMENT_ROOT.'/poa/class/poaprocesscontratext.class.php';
	$objpcont = new Poaprocesscontratext($db);
	$objpcont->getlist_contrat2($id,$statutppc);
	if (count($objpcont->aContrat)>0)
	{
		foreach ((array) $objpcont->aContrat AS $fk_contrat => $rowidcontrat)
		{
			$array[$fk_contrat] = $rowidcontrat;
		}
		return $array;
	}
}

function listpayment($id,$fk_contrat)
{
	global $db;
	require_once DOL_DOCUMENT_ROOT.'/poa/class/poapartidadevext.class.php';
	$objd = new Poapartidadevext($db);
	$objd->get_sum_pcp2($id,$fk_contrat);
	if (count($objd->array)>0)
		return $objd->array;
}
function zerofill($valor,$longitud)
{
	$res = str_pad($valor, $longitud,'0', STR_PAD_LEFT);
	return $res;
}
//getlistareason

function getlistareason($id,$fk_user=0)
{
	global $langs, $db, $user;
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentext.class.php';
	require_once DOL_DOCUMENT_ROOT.'/orgman/class/pdepartamentuserext.class.php';
	//nuevamente buscamos si tiene hijos del hijo
	$objaa = new Pdepartamentext($db);
	$objau = new Pdepartamentuserext($db);
	$objaa->getlist_son($id);
	$objau->getlist($id,($user->admin?0:$fk_user));
	foreach((array) $objau->array AS $h => $objh)
	{
		$aPrivilege[$objh->fk_area] = $objh->privilege;
	}
	$aArea[$id]['id'] = $id;
	$aArea[$id]['privilege'] = $aPrivilege[$id];

	if (count($objaa->array) > 0)
	{
		foreach ((array) $objaa->array AS $k => $objara)
		{
			$aArea[$objara->id]['id'] = $objara->id;
			$aArea[$objara->id]['privilege'] = $aPrivilege[$id];
				//nuevamente buscamos si tiene hijos del hijo
			$objab = new Pdepartamentext($db);
			$objab->getlist_son($objara->id);
			if (count($objab->array) > 0)
			{
				foreach ((array) $objab->array AS $k => $objarb)
				{
					$aArea[$objarb->id]['id'] = $objarb->id;
					$aArea[$objarb->id]['privilege'] = $aPrivilege[$id];
					//nuevamente buscamos si tiene hijos del hijo
					$objac = new Pdepartamentext($db);
					$objac->getlist_son($objarb->id);
					if (count($objac->array) > 0)
					{
						foreach ((array) $objac->array AS $k => $objarc)
						{
							$aArea[$objarc->id]['id'] = $objarc->id;
							$aArea[$objarc->id]['privilege'] = $aPrivilege[$id];
						}
					}
				}
			}
		}
	}
	return $aArea;
}

/**
 *  Show tab footer of a card
 *
 *  @param	object	$object			Object to show
 *  @param	string	$paramid   		Name of parameter to use to name the id into the URL next/previous link
 *  @param	string	$morehtml  		More html content to output just before the nav bar
 *  @param	int		$shownav	  	Show Condition (navigation is shown if value is 1)
 *  @param	string	$fieldid   		Nom du champ en base a utiliser pour select next et previous (we make the select max and min on this field)
 *  @param	string	$fieldref   	Nom du champ objet ref (object->ref) a utiliser pour select next et previous
 *  @param	string	$morehtmlref  	More html to show after ref
 *  @param	string	$moreparam  	More param to add in nav link url.
 *	@param	int		$nodbprefix		Do not include DB prefix to forge table name
 *	@param	string	$morehtmlleft	More html code to show before ref
 *	@param	string	$morehtmlright	More html code to show before navigation arrows
 *  @return	void
 */
function dol_banner_tabpoa($object, $paramid, $morehtml='', $shownav=1, $fieldid='rowid', $fieldref='ref', $morehtmlref='', $moreparam='', $nodbprefix=0, $morehtmlleft='', $morehtmlright='')
{
	global $conf, $form, $formv, $user, $langs;

	$maxvisiblephotos=1;
	$showimage=1;
	$showbarcode=empty($conf->barcode->enabled)?0:($object->barcode?1:0);
	if (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->barcode->lire_advance)) $showbarcode=0;
	$modulepart='unknown';
	if ($object->element == 'societe') $modulepart='societe';
	if ($object->element == 'contact') $modulepart='contact';
	if ($object->element == 'member') $modulepart='memberphoto';
	if ($object->element == 'user') $modulepart='userphoto';
	if ($object->element == 'product') $modulepart='product';

	print '<div class="arearef heightref valignmiddle" width="100%">';
	if ($object->element == 'product')
	{
		$width=80; $cssclass='photoref';
		$showimage=$object->is_photo_available($conf->product->multidir_output[$object->entity]);
		$maxvisiblephotos=(isset($conf->global->PRODUCT_MAX_VISIBLE_PHOTO)?$conf->global->PRODUCT_MAX_VISIBLE_PHOTO:5);
		if ($conf->browser->phone) $maxvisiblephotos=1;
		if ($showimage) $morehtmlleft.='<div class="floatleft inline-block valignmiddle divphotoref">'.$object->show_photos($conf->product->multidir_output[$object->entity],'small',-$maxvisiblephotos,0,0,0,$width,0).'</div>';
		else
		{
			$nophoto='/public/theme/common/nophoto.png';
			$morehtmlleft.='<div class="floatleft inline-block valignmiddle divphotoref"><img class="photo'.$modulepart.($cssclass?' '.$cssclass:'').'" alt="No photo" border="0"'.($width?' width="'.$width.'"':'').($height?' height="'.$height.'"':'').' src="'.DOL_URL_ROOT.$nophoto.'"></div>';
		}
	}
	else
	{
		if ($showimage) $morehtmlleft.='<div class="floatleft inline-block valignmiddle divphotoref">'.$form->showphoto($modulepart,$object,0,0,0,'photoref','small',1,0,$maxvisiblephotos).'</div>';
	}
	if ($showbarcode) $morehtmlleft.='<div class="floatleft inline-block valignmiddle divphotoref">'.$form->showbarcode($object).'</div>';
	if ($object->element == 'societe' && ! empty($conf->use_javascript_ajax) && $user->rights->societe->creer && ! empty($conf->global->MAIN_DIRECT_STATUS_UPDATE)) {
		$morehtmlright.=ajax_object_onoff($object, 'status', 'status', 'InActivity', 'ActivityCeased');
	}
	elseif ($object->element == 'product')
	{
	    //$morehtmlright.=$langs->trans("Status").' ('.$langs->trans("Sell").') ';
		if (! empty($conf->use_javascript_ajax) && $user->rights->produit->creer && ! empty($conf->global->MAIN_DIRECT_STATUS_UPDATE)) {
			$morehtmlright.=ajax_object_onoff($object, 'status', 'tosell', 'ProductStatusOnSell', 'ProductStatusNotOnSell');
		} else {
			$morehtmlright.=$object->getLibStatut(2,0);
		}
		$morehtmlright.=' &nbsp; ';
        //$morehtmlright.=$langs->trans("Status").' ('.$langs->trans("Buy").') ';
		if (! empty($conf->use_javascript_ajax) && $user->rights->produit->creer && ! empty($conf->global->MAIN_DIRECT_STATUS_UPDATE)) {
			$morehtmlright.=ajax_object_onoff($object, 'status_buy', 'tobuy', 'ProductStatusOnBuy', 'ProductStatusNotOnBuy');
		} else {
			$morehtmlright.=$object->getLibStatut(2,1);
		}
	}
	else {
		$morehtmlright.=$object->getLibStatut(2);
	}
	if (! empty($object->name_alias)) $morehtmlref.='<div class="refidno">'.$object->name_alias.'</div>';      // For thirdparty
	if (! empty($object->label))      $morehtmlref.='<div class="refidno">'.$object->label.'</div>';           // For product
	if ($object->element != 'product')
	{
		$morehtmlref.='<div class="refidno">';
		$morehtmlref.=$object->getBannerAddress('refaddress',$object);
		$morehtmlref.='</div>';
	}
	if (! empty($conf->global->MAIN_SHOW_TECHNICAL_ID) && in_array($object->element, array('societe', 'contact', 'member', 'product')))
	{
		$morehtmlref.='<div style="clear: both;"></div><div class="refidno">';
		$morehtmlref.=$langs->trans("TechnicalID").': '.$object->id;
		$morehtmlref.='</div>';
	}
	$morehtmlright='';
	print $formv->showrefnavpoa($object, $paramid, $morehtml, $shownav, $fieldid, $fieldref, $morehtmlref, $moreparam, $nodbprefix, $morehtmlleft, $morehtmlright);
	print '</div>';
	print '<div class="underrefbanner clearboth"></div>';
}

function planstrategic_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('poa');
	$head = array();

	$h = 0;
	$head[$h][0] = dol_buildpath("/poa/plan/card.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("PEI");
	$head[$h][2] = 'card';

	$h++;
	$head[$h][0] = dol_buildpath("/poa/objetive/objetive.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Objetives");
	$head[$h][2] = 'objetive';

	$h++;
	$head[$h][0] = dol_buildpath("/poa/structure/catprog.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Catprog");
	$head[$h][2] = 'catprog';

	$h++;
	$head[$h][0] = dol_buildpath("/poa/partida/partida.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Metas");
	$head[$h][2] = 'meta';

	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'planstrategic');

	return $head;
}

function preventive_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('poa');
	$head = array();

	$h = 0;
	$head[$h][0] = dol_buildpath("/poa/execution/fiche.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Card");
	$head[$h][2] = 'card';


	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'preventive');

	return $head;
}

function structure_prepare_head($object,$urladd='')
{
	global $langs, $conf;
	$langs->load('poa');
	$head = array();

	$h = 0;
	$head[$h][0] = dol_buildpath("/poa/structure/list.php?id=".$object->id.($urladd?$urladd:''),1);
	$head[$h][1] = $langs->trans("Catprog");
	$head[$h][2] = 'catprog';

	//$h++;
	//$head[$h][0] = dol_buildpath("/poa/partida/rrhh.php?id=".$object->id.($urladd?$urladd:''),1);
	//$head[$h][1] = $langs->trans("RRHH");
	//$head[$h][2] = 'rrhh';

	//$h++;
	//$head[$h][0] = dol_buildpath("/poa/partida/partida.php?id=".$object->id.($urladd?$urladd:''),1);
	//$head[$h][1] = $langs->trans("Programmingexpenses");
	//$head[$h][2] = 'expense';

	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'estructure');

	return $head;
}

function structureinp_prepare_head($object,$urladd='')
{
	global $langs, $conf;
	$langs->load('poa');
	$head = array();

	$h = 0;
	$head[$h][0] = dol_buildpath("/poa/structure/listinp.php?id=".$object->id.($urladd?$urladd:''),1);
	$head[$h][1] = $langs->trans("Catprog");
	$head[$h][2] = 'catprog';

	$h++;
	$head[$h][0] = dol_buildpath("/poa/partida/resource.php?id=".$object->id.($urladd?$urladd:''),1);
	$head[$h][1] = $langs->trans("Resourcescheduling");
	$head[$h][2] = 'resource';

	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'estructureinp');

	return $head;
}
function poaplan_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('poa@poa');
	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/poa/poaplan/card.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Card");
	$head[$h][2] = 'card';
	$h++;

	$head[$h][0] = dol_buildpath("/poa/poaplan/carddet.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Details");
	$head[$h][2] = 'carddet';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'poa_plan');

	return $head;
}

function valida_month_poa($objpoa,$arrayMonth)
{
	global $langs;
	if (!is_object($objpoa)) return -1;

	$aText = array(1=>'jan',2=>'feb',3=>'mar',4=>'apr',5=>'may',6=>'jun',7=>'jul',8=>'aug',9=>'sep',10=>'oct',11=>'nov',12=>'dec');
	foreach ($aText AS $mes => $text)
	{
		$mtext = 'm_'.$text;
		$ptext = 'p_'.$text;
		if ($objpoa->$mtext>0 && $objpoa->$ptext <=0)
		{
			$error++;
			setEventMessages($langs->trans('No existe producto en el mes de').' '.$arrayMonth[$mes],null,'errors');
		}
		if ($objpoa->$mtext<=0 && $objpoa->$ptext >0)
		{
			$error++;
			setEventMessages($langs->trans('No existe importe en el mes de').' '.$arrayMonth[$mes],null,'errors');
		}
	}
	return $error;
}

function get_type()
{
	global $langs;
	$aType = array(1=>$langs->trans('Desarrollo'),2=>$langs->trans('Funcionamiento'));
	$aTypec = array(1=>$langs->trans('D'),2=>$langs->trans('F'));
	return array($aType,$aTypec);
}


function verif_permission($db, User $user)
{
	global $conf,$langs;
	//verificación de permisos para aprobacion de inicios de procesos
	require_once DOL_DOCUMENT_ROOT.'/poa/class/poapermissionsext.class.php';
	$objPermission = new Poapermissionsext($db);
	$now = dol_now();
	$inow = $db->idate($now);
	$filter = " AND t.fk_user = ".$user->id;
	$filter.= " AND ". $inow .">= t.date_ini AND ".$inow. "<= t.date_fin" ;
	$res = $objPermission->fetchAll('','',0,0,array(1=>1),'AND',$filter,true);
	if ($res>0)
	{
		//si existe tiene permiso
		return true;
	}
	else
		return false;
}
?>
