<?php
/* Copyright (C) 2003-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis.houssin@capnetworks.com>
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
 *	\file       htdocs/core/boxes/box_clients.php
 *	\ingroup    societes
 *	\brief      Module de generation de l'affichage de la box clients
 */

include_once DOL_DOCUMENT_ROOT.'/core/boxes/modules_boxes.php';


/**
 * Class to manage the box to show last thirdparties
 */
class box_exchangerate extends ModeleBoxes
{
	var $boxcode="lastexchangerate";
	var $boximg="object_multicurrency";
	var $boxlabel="BoxLastExchangeRate";
	//var $depends = array("multicurrency");

	var $db;
	var $param;

	var $info_box_head = array();
	var $info_box_contents = array();


	/**
     *  Load data for box to show them later
     *
     *  @param	int		$max        Maximum number of records to load
     *  @return	void
	 */
	function loadBox($max=5)
	{
	  global $user, $langs, $db, $conf;
	  $langs->load("boxes");
	  $langs->load("multicurrency@multicurrency");
	  
	  echo '<hr>max '.$this->max=$max;
	  
	  include_once DOL_DOCUMENT_ROOT.'/multicurren/currency/class/cscurrencytype.class.php';

	  $objectct = new Cscurrencytype($db);
	  $objectct->get_currency_type_array();

	  $this->info_box_head = array('text' => $langs->trans("BoxTitleLastModifiedExchangeRate",$max));
	  
	  $sql = "SELECT s.country, s.rowid as id, s.date_ind, s.currency1, s.currency2, s.currency3, s.currency4, s.currency5, s.currency6";
	  $sql.= " FROM ".MAIN_DB_PREFIX."cs_indexes as s";
	  $sql.= " ORDER BY s.date_ind DESC";
	  echo $sql.= $db->plimit($max, 0);
	  
	  dol_syslog(get_class($this)."::loadBox sql=".$sql,LOG_DEBUG);
	  $result = $db->query($sql);
	  if ($result)
	    {
	      $num = $db->num_rows($result);
	      $url= DOL_URL_ROOT."/multicurren/exchangerate/fiche.php?id=";
	      
	      $i = 0;
	      while ($i < $num)
		{
		  $objp = $db->fetch_object($result);
		  $dateind=$db->jdate($objp->date_ind);
		  
		  $this->info_box_contents[$i][0] = array('td' => 'align="left" width="16"',
							  'logo' => $this->boximg,
							  'url' => $url.$objp->id);
		  
		  $this->info_box_contents[$i][1] = array('td' => 'align="right"',
							  'text' => dol_print_date($dateind, "day"));
		  $j = 1
		  foreach ((array) $objectct->array AS $k => $objdata)
		    {
		      if ($j == 1)
			{
			  $currency = 'currency'.$j;
			  $this->info_box_contents[$i][2] = array('td' => 'align="right" width="18"',
								'text' => $objp->$currency);
			}
		      $j++;
		    }
		  $i++;
		}
	      
	      if ($num==0) $this->info_box_contents[$i][0] = array('td' => 'align="center"','text'=>$langs->trans("NoRecordedCustomers"));
	      
	      $db->free($result);
	    }
	  else
	    {
	      $this->info_box_contents[0][0] = array(	'td' => 'align="left"',
							'maxlength'=>500,
							'text' => ($db->error().' sql='.$sql));
	    }
	  
	}

	/**
	 *	Method to show box
	 *
	 *	@param	array	$head       Array with properties of box title
	 *	@param  array	$contents   Array with properties of box lines
	 *	@return	void
	 */
	function showBox($head = null, $contents = null)
	{
		parent::showBox($this->info_box_head, $this->info_box_contents);
	}

}

?>
