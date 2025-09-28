<?php
/* Copyright (C) 2012      Christophe Battarel  <christophe.battarel@altairis.fr>
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
 *
 */

/**
 *	\file       /htdocs/fourn/ajax/getSupplierPrices.php
 *	\brief      File to return Ajax response on get supplier prices
 */

if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1'); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');

require '../../main.inc.php';

$fk_equipment=GETPOST('fk_equipment','int');

$prices = array();
$langs->load('mant@mant');

/*
 * View
*/

top_httphead();

//print '<!-- Ajax page called with url '.$_SERVER["PHP_SELF"].'?'.$_SERVER["QUERY_STRING"].' -->'."\n";

if (! empty($fk_equipment))
  {
    $sql = "SELECT p.rowid, p.descrip AS label, p.ref";
    $sql.= " FROM ".MAIN_DB_PREFIX."assets as p";
    $sql.= " WHERE p.rowid = ".$fk_equipment;
    $sql.= " ORDER BY p.descrip DESC";
    
    dol_syslog("Ajax::getSupplier sql=".$sql, LOG_DEBUG);
    $result=$db->query($sql);
    
    if ($result)
      {
	$num = $db->num_rows($result);
	
	if ($num)
	  {
	    $i = 0;
	    while ($i < $num)
	      {
		$objp = $db->fetch_object($result);
		
		// $price = $objp->fprice * (1 - $objp->remise_percent / 100);
		// $unitprice = $objp->unitprice * (1 - $objp->remise_percent / 100);
		
		$title = $objp->label.' - '.$objp->ref;
				
		$prices[] = array("id" => $objp->rowid, 
				  "label" => $label, 
				  "title" => $title);
		$i++;
	      }
	    
	    $db->free($result);
	  }
      }
  }

echo json_encode($prices);

?>
