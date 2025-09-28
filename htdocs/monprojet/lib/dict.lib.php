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

function select_type_deduction($selected='',$htmlname='type_group',$htmloption='',$showempty=0,$showlabel=0,$campoid='rowid')
{
  global $db, $langs, $conf;
  $sql = "SELECT f.rowid, f.code AS code, f.label AS libelle FROM ".MAIN_DB_PREFIX."c_deductions AS f ";
  $sql.= " WHERE ";
  $sql.= " f.active = 1";
  $sql.= " ORDER BY f.label";
  $resql = $db->query($sql);
  $html = '';

  if ($resql)
    $html = htmlselect($resql,$selected,$htmlname,$htmloption,$showempty,$showlabel,$campoid);
  return $html;
}

/*
 campos obligatorios de la tabla
 rowid
 code
 libelle
*/
function htmlselect($resql,$selected='',$htmlname='type_group',$htmloption='',$showempty=0,$showlabel=0,$campoid='rowid')
{
  global $langs,$db,$conf;
  $html.= '<select class="flat" name="'.$htmlname.'" id="select'.$htmlname.'">';
  if ($showempty) 
    $html.= '<option value="0">&nbsp;</option>';
  $num = $db->num_rows($resql);
  $i = 0;
  if ($num)
    {
      while ($i < $num)
	{
	  $obj = $db->fetch_object($resql);
	  if (!empty($selected) && $selected == $obj->$campoid)
	    {
	      $html.= '<option value="'.$obj->$campoid.'" selected="selected">'.$obj->libelle.'</option>';
	      if ($showlabel)
		return $obj->libelle;
	    }
	  else
	    {
	      $html.= '<option value="'.$obj->$campoid.'">'.$langs->trans($obj->libelle);
	      if (!empty($obj->code) && $campoid == 'rowid')
		$html.= ' ('.$obj->code.')';
	      $html.= '</option>';
	      
	    }
	  $i++;
	}
    }
  $html.= '</select>';
  return $html;

}

function getlist_deduction($filter = '',$sortorder='ASC',$sortfield="label")
{
  global $db, $langs, $conf;
  $sql = "SELECT f.rowid, f.code AS code, f.label, f.sequence FROM ".MAIN_DB_PREFIX."c_deductions AS f ";
  $sql.= " WHERE ";
  $sql.= " f.active = 1";
  if ($filter) $sql.= $filter;
  if (!empty($sortfield)) {
    $sql .= ' ORDER BY ' . $sortfield . ' ' . $sortorder;
  }
  $resql = $db->query($sql);
  $num = $db->num_rows($resql);
  $n = 0;
  $array = array();
  while ($obj = $db->fetch_object($resql)) {
    
    $array[$n]['id']    = $obj->rowid;
    $array[$n]['code']  = $obj->code;
    $array[$n]['label'] = $obj->label;
    $n++;
  }
  return $array;
  
}

?>