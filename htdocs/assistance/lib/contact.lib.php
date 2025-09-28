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


function getlist_contact($socid)
{
  global $langs,$db;
  $sql = "SELECT d.rowid AS login, d.lastname, d.firstname, d.rowid ";
  $sql.= " FROM ".MAIN_DB_PREFIX."socpeople as d";
  $sql.= " WHERE d.statut >0 ";
  
  dol_syslog("getlist_contact sql=".$sql);
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
      dol_syslog("getlist_contact ".$error, LOG_ERR);
      return -1;
    }
}

?>