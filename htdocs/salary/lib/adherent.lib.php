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
 *	\file       salary/lib/adherent.lib.php
 *	\brief      Ensemble de fonctions de base pour le module Salary
 * 	\ingroup	salary
 */

function select_adherent($selected='',$htmlname='fk_adherent',$htmloption='',$maxlength=0,$showempty=0)
{
  global $langs,$db,$conf;
  $out='';
  $countryArray=array();
  $label=array();
  // if (substr($conf->global->MAIN_VERSION_LAST_INSTALL,0,3)*1 >= 3.4)
  //   {
      if (STRTOUPPER($conf->db->type) == 'PGSQL')

	$sql = "SELECT d.rowid, d.login as code_iso, (d.lastname || ' ' || d.firstname) AS label ";
      else
	$sql = "SELECT d.rowid, d.login as code_iso, CONCAT(d.lastname,' ',d.firstname) AS label ";

      //    }
  // else
  //     if (STRTOUPPER($conf->db->type) == 'PGSQL')
  // 	$sql = "SELECT d.rowid, d.login as code_iso, (d.prenom || ' ' || d.nom) AS label ";
  //     else
  // 	$sql = "SELECT d.rowid, d.login as code_iso, CONCAT(d.prenom,' ',d.nom) AS label ";
	
  //  $sql = "SELECT d.rowid, d.login as code_iso, (d.prenom||', '||d.nom) as label ";
  $sql.= " FROM ".MAIN_DB_PREFIX."adherent_type as t ";  
  $sql.= " INNER JOIN ".MAIN_DB_PREFIX."adherent as d ON d.fk_adherent_type = t.rowid ";
  $sql.= " WHERE d.entity IN (".getEntity().")";
  // if (substr($conf->global->MAIN_VERSION_LAST_INSTALL,0,3)*1 >= 3.4)
    $sql.= " ORDER BY d.lastname,d.firstname";
  // else
  //   $sql.= " ORDER BY d.nom,d.prenom";
  
  //  dol_syslog(get_class($this)."::select_adherent sql=".$sql);
  $resql=$db->query($sql);
  if ($resql)
    {
      $out = select_generic($resql,$showempty,$htmlname,$htmloption,$campo,$selected);
    }
  else
    {
      dol_print_error($db);
    }
  
  return $out;
}
?>