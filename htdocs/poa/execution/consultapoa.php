<?php
  /*
 * Copyright (C) 2014 Ramiro Queso  <ramiro@ubuntu-bo.com>
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
 *	\file       htdocs/poa/execution/consultapoa.php
 *	\ingroup    poa
 *	\brief      Include to show main page for POA module
 */

require_once("../../main.inc.php");
if(
   isset($_GET['fk_poa']) &&
   $_GET['fk_poa'] != null
   )
  {
    $sql = "SELECT s.rowid, s.nom as name, s.amount ";
    $sql.= " FROM ".MAIN_DB_PREFIX."poa_pac as s";
    $sql.= " WHERE s.rowid = '".$_GET['fk_poa']."'";
    $result=$db->query($sql);
    if ($result)
      {
	$num = $db->num_rows($result);
	if ($db->num_rows($result))
	  {
	    $obj = $db->fetch_object($result);
	    $valor = $obj->name;
	    
	  }
	else
	  $valor = '';
	//$db->free($result);
	
      }
    else
      {
	$valor = '22222';
	//dol_print_error($db);
      }

    /*
     * Aquí haces el resto de script, asegúrate de validar bien
     * la cédula con la función mysql_real_escape_string() de php 
     * para evitar todo tipo de injección posible.
     */
    print '<script type="text/javascript">';
    print ' window.parent.document.getElementById('."'label'".').value = "'. $valor.'"'; 
    print '</script>';
    
  }

?>
