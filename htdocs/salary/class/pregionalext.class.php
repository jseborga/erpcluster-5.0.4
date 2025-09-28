<?php
require_once DOL_DOCUMENT_ROOT.'/salary/class/pregional.class.php';

/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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
 *  \file       dev/skeletons/pregional.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Initialy built by build_class_from_table on 2013-09-11 21:54
 */

//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class Pregionalext extends Pregional
{


	/**
	 *  Return combo list of activated countries, into language of user
	 *
	 *  @param	string	$selected       Id or Code or Label of preselected country
	 *  @param  string	$htmlname       Name of html select object
	 *  @param  string	$htmloption     Options html on select object
	 *  @param	string	$maxlength		Max length for labels (0=no limit)
	 *  @return string           		HTML string with select
	 */
	function select_regional($selected='',$htmlname='fk_regional',$htmloption='',$maxlength=0,$showempty=0)
	{
		global $conf,$langs;

		$langs->load("salary@salary");

		$out='';
		$countryArray=array();
		$label=array();

		$sql = "SELECT c.rowid, c.ref as code_iso, c.label as label";
		$sql.= " FROM ".MAIN_DB_PREFIX."p_regional AS c ";
		$sql.= " WHERE c.entity = ".$conf->entity;
		$sql.= " ORDER BY c.ref ASC";

		dol_syslog(get_class($this)."::select_regional sql=".$sql);
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
				$countryArray[$i]['label']		= ( $obj->code_iso && $langs->transnoentitiesnoconv("Regional".$obj->code_iso)!="Regional".$obj->code_iso?$langs->transnoentitiesnoconv("Regional".$obj->code_iso):($obj->label!='-'?$obj->label:''));
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
