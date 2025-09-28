<?php
/* Copyright (C) 2014 Ramiro Queso  <ramiro@ubuntu-bo.com>
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
 *	\file       mant/lib/societe.lib.php
 *	\brief      Ensemble de fonctions de base pour le module Mant
 * 	\ingroup	mant
 */

function select_societe($selected='',$htmlname='fk_adherent',$htmloption='',$maxlength=0,$showempty=0,$nodefined=0)
{
  global $langs,$db,$conf;
  $out='';
  $countryArray=array();
  $label=array();
  $sql = "SELECT d.rowid, d.nom AS label ";
  
  $sql.= " FROM ".MAIN_DB_PREFIX."societe as d ";  
  $sql.= " WHERE d.entity IN (".getEntity().")";
  $sql.= " ORDER BY d.nom ";
  $resql=$db->query($sql);
  if ($resql)
    {
      $out = select_generic($resql,$showempty,$htmlname,$htmloption,$campo,$selected,$nodefined);
    }
  else
    {
      dol_print_error($db);
    }
  
  return $out;
}
?>