<?php
/* Copyright (C) 2013 Ramiro Queso  <ramiro@ubuntu-bo.com>
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * or see http://www.gnu.org/
 */

/**
 *	\file       projetmonitoring/lib/projetmonitoring.lib.php
 *	\brief      Ensemble de fonctions de base pour le module Project Monitoring
 * 	\ingroup	projetmonitoring
 */


function fabrication_prepare_head($object)
{
	global $langs, $conf;
	$langs->load('fabrication@fabrication');

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/product/card.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Product");
	$head[$h][2] = 'Product';
	$h++;
	$head[$h][0] = dol_buildpath("/fabrication/productlist/fiche.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Material");
	$head[$h][2] = 'material';
	$h++;
	$head[$h][0] = dol_buildpath("/fabrication/portioning/fiche.php?id=".$object->id,1);
	$head[$h][1] = $langs->trans("Portioning");
	$head[$h][2] = 'portioning';
	//$h++;
	//$head[$h][0] = dol_buildpath("/fabrication/units/fiche.php?id=".$object->id,1);
	//$head[$h][1] = $langs->trans("Unit");
	//$head[$h][2] = 'unit';


	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	// $this->tabs = array('entity:+tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to add new tab
	// $this->tabs = array('entity:-tabname:Title:@mymodule:/mymodule/mypage.php?id=__ID__');   to remove a tab
	complete_head_from_modules($conf,$langs,$object,$head,$h,'fabrication');
	return $head;
}

function select_unit($selected='',$htmlname='code',$htmloption='',$showempty=0,$showlabel=0,$campo='code',$label='label')
{
	global $db, $langs, $conf,$user;

	$sql = "SELECT f.rowid, f.code, f.label FROM ".MAIN_DB_PREFIX."c_units AS f ";
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
					$html.= '<option value="'.$obj->$campo.'" selected="selected">'.$langs->trans($obj->$label).'</option>';
					if ($showlabel)
					{
						return $langs->trans($obj->$label);
					}
				}
				else
				{
					$html.= '<option value="'.$obj->$campo.'">'.$langs->trans($obj->$label).'</option>';
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


?>