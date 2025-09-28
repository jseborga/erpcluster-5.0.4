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

function select_adherent($selected='',$htmlname='fk_adherent',$htmloption='',$maxlength=0,$showempty=0,$active=1)
{
  global $langs,$db,$conf;
  $out='';
  $countryArray=array();
  $label=array();
  if (STRTOUPPER($conf->db->type) == 'PGSQL')
    
    $sql = "SELECT d.rowid, d.login as code_iso, (d.lastname || ' ' || d.firstname) AS label ";
  else
    $sql = "SELECT d.rowid, d.login as code_iso, CONCAT(d.lastname,' ',d.firstname) AS label ";
  
  $sql.= " FROM ".MAIN_DB_PREFIX."adherent_type as t ";  
  $sql.= " INNER JOIN ".MAIN_DB_PREFIX."adherent as d ON d.fk_adherent_type = t.rowid ";
  $sql.= " WHERE d.entity IN (".getEntity().")";
  if ($active)
    $sql.= " AND d.statut = 1";
  $sql.= " ORDER BY d.lastname,d.firstname";
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

/**
 *	Load member from database
 *
 *	@param	int		$rowid      Id of object to load
 * 	@param	string	$ref		To load member from its ref
 * 	@param	int		$fk_soc		To load member from its link to third party
 * 	@param	int		$ref_ext	External reference
 *	@return int         		>0 if OK, 0 if not found, <0 if KO
 */
function adherent_fetch($rowid,$email='')
{
  global $langs,$db;
  
  $sql = "SELECT d.rowid, d.email ";
  $sql.= " FROM ".MAIN_DB_PREFIX."adherent as d";
  if ($rowid) 
    {
      $sql.= " WHERE d.rowid=".$rowid;
    }
  elseif ($email)
    {
      $sql.= " WHERE d.email='".$db->escape($email)."'";
    }
  
  dol_syslog("adherent_fetch sql=".$sql);
  $resql=$db->query($sql);
  if ($resql)
    {
      if ($db->num_rows($resql))
	{
	  $obj = $db->fetch_object($resql);
	  return $obj->rowid;
	}
      else
	{
	  return 0;
	}
    }
  else
    {
      $error=$db->lasterror();
      dol_syslog("adherent_fetch ".$error, LOG_ERR);
      return -1;
    }
}

function adherent_fetch_ext($rowid)
{
  global $langs,$db;
  
  $sql = "SELECT d.fk_charge, d.fk_departament, d.internal ";
  $sql.= " FROM ".MAIN_DB_PREFIX."adherent_extrafields as d";
  $sql.= " WHERE d.fk_object=".$rowid;
  
  dol_syslog("adherent_fetch_ext sql=".$sql);
  $resql=$db->query($sql);
  $aArray = array();
  if ($resql)
    {
      if ($db->num_rows($resql))
	{
	  $obj = $db->fetch_object($resql);
	  $aArray['fk_charge'] = $obj->fk_charge;
	  $aArray['fk_departament'] = $obj->fk_departament;
	  $aArray['internal'] = $obj->internal;
	  return $aArray;
	}
      else
	{
	  return 0;
	}
    }
  else
    {
      $error=$db->lasterror();
      dol_syslog("adherent_fetch_ext ".$error, LOG_ERR);
      return -1;
    }
}

function getlist_adherent()
{
  global $langs,$db;
  $sql = "SELECT d.login, d.lastname, d.firstname, d.rowid ";
  $sql.= " FROM ".MAIN_DB_PREFIX."adherent as d";
  $sql.= " WHERE d.statut >=0 ";
  
  dol_syslog("getlist_adherent sql=".$sql);
  $resql=$db->query($sql);
  $aArray = array();
  if ($resql)
    {
      $num = $db->num_rows($resql);
      if ($db->num_rows($resql))
	{
	  $i = 0;
	  while ($i < $num)
	    {
	      $obj = $db->fetch_object($resql);
	      $aArray[$obj->rowid]['login'] = $obj->login;
	      $aArray[$obj->rowid]['name'] = $obj->lastname.' '.$obj->firstname;
	      $i++;
	    }
	  return $aArray;
	}
      else
	{
	  return array();
	}
    }
  else
    {
      $error=$db->lasterror();
      dol_syslog("getlist_adherent ".$error, LOG_ERR);
      return -1;
    }
}

?>