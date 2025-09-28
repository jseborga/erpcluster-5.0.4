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
 *	\file       mant/lib/user.lib.php
 *	\brief      Ensemble de fonctions de base pour le module Mant
 * 	\ingroup	Mant
 */

function list_user_member($filter='',$fk_soc='',$exclued='')
{
  global $langs,$db,$conf,$objUser,$objSoc;
  $aArray=array();
  
  $sql = "SELECT d.rowid, d.rowid ";
  
  $sql.= " FROM ".MAIN_DB_PREFIX."user as d ";  
  if ($user->societe_id)
    $sql.= " WHERE d.entity IN (".getEntity().'0'.")";
  else
    $sql.= " WHERE d.entity IN (".getEntity().")";
  if (!empty($filter))
    $sql.= " AND ".$filter;
  $resql=$db->query($sql);
  $objSoc->typent_id;
  if ($resql)
    {
      $num = $db->num_rows($resql);
      if ($num)
	{
	  $i = 0;
	  while ($i < $num)
	    {
	      $obj = $db->fetch_object($resql);
	      $lAdd = false;
	      //buscamos al user
	      $obju = new User($db);
	      if ($obju->fetch($obj->rowid)>0)
		{
		  if ($obju->id == $obj->rowid)
		    {
		      if ($exclued == 'E')
			{
			  if ($obju->array_options['options_fk_tercero'] != $objSoc->typent_id)
			    $lAdd = true;
			  elseif(empty($obju->array_options['options_fk_tercero']))
			    $lAdd = true;
			  else
			    $lAdd = false;
			  if ($obju->admin)
			    $lAdd = true;
			}
		      else
			{
			  if (!empty($fk_soc) && $obju->array_options['options_fk_tercero'] == $objSoc->typent_id)
			    $lAdd = true;
			  else
			    $lAdd = false;
			}
		    }
		}
	      if ($lAdd)
		$aArray[$obj->rowid] = $obj->rowid;
	      $i++;
	    }
	  return $aArray;
	}
      else
	{
	  return 0;
	}
    }
  else
    {
      dol_print_error($db);
    }
}


?>